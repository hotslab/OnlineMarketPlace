<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Bus;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use App\Jobs\ProcessStripePaymentIntent;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_submit_email_address_on_checkout_and_receive_client_token_to_capture_stripe_payment_details()
    {
        $product = Product::factory()->create();
        $response = $this->from(route('purchases.checkout', [ 'id' => $product->id ]))->post(route('purchases.client'), [
            'email' => 'baba-yaga@continental.com',
            'ignoreSavedDetails' => false
        ]);
        $response->assertValid(['email']);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('code', 200);
        $response->assertJsonStructure([
            'hasSavedDetails',
            'clientSecret',
            'customer',
            'setupIntent',
            'code',
            'status'
        ]);
    }

    public function test_submit_payment_and_product_details_and_then_check_if_purchase_saved_in_database()
    {
        $product = Product::factory()->create();
        $response = $this->from(route('purchases.checkout', [ 'id' => $product->id ]))->post(route('purchases.purchase'), [
            'productID' => $product->id,
            'email' => 'baba-yaga@continental.com',
            'paidAmount' => $product->price
        ]);
        $response->assertJsonPath('code', 200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonStructure([
            'redirectURL',
            'deleteURL',
            'code',
            'status'
        ]);
        $this->assertDatabaseHas('purchases', [
            'email' => 'baba-yaga@continental.com',
            'product_id' => $product->id,
            'paid_amount' => $product->price,
            'is_deposit' => false
        ]);
        $purchase = Purchase::first();
        $response->assertJsonPath('redirectURL', route('purchases.confirmation', ['id' => $purchase->id]));
        $response->assertJsonPath('deleteURL', route('purchases.delete', ['id' => $purchase->id]));
    }

    public function test_payment_confirmation_using_stripe_for_purchase()
    {
        Bus::fake();
        $product = Product::factory()->create();
        $purchase = Purchase::create([
            'email' => 'baba-yaga@continental.com',
            'product_id' => $product->id,
            'paid_amount' => $product->price,
            'is_deposit' => false
        ]);
        $response = $this->from(route('purchases.checkout', [ 'id' => $product->id ]))->get(route('purchases.confirmation', ['id' => $purchase->id]));
        $response->assertOk();
        Bus::assertDispatchedAfterResponse(ProcessStripePaymentIntent::class);
    }

    public function test_purchase_deleted()
    {
        $product = Product::factory()->create();
        $purchase = Purchase::create([
            'email' => 'baba-yaga@continental.com',
            'product_id' => $product->id,
            'paid_amount' => $product->price,
            'is_deposit' => false
        ]);
        $response = $this->from(route('purchases.checkout', [ 'id' => $product->id ]))->delete(route('purchases.delete', ['id' => $purchase->id]));
        $response->assertOk();
        $this->assertDatabaseMissing('purchases', [
            'email' => 'baba-yaga@continental.com',
            'product_id' => $product->id,
            'paid_amount' => $product->price,
            'is_deposit' => false
        ]);
    }
}
