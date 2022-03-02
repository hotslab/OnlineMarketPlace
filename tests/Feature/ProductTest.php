<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\UserProduct;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_user_product_and_check_if_saved_and_redirected_back_to_userproducts_view()
    {
        Storage::fake('products');
        $user = User::factory()->create();
        $this->assertGuest();
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);
        $file = UploadedFile::fake()->image('table.jpg');
        $response = $this->from(route('userproducts.view', [ 'id' => $user->id ]))->post(route('userproducts.store'), [
            'name' => 'Large Table',
            'price' => 33.97,
            'image' =>  $file
        ]);
        $response->assertValid(['name', 'price', 'image']);
        $response->assertRedirect(route('userproducts.view', ['id' => $user->id]));
        Storage::disk('products')->assertExists('products/'.$file->hashName());
        $this->assertDatabaseHas('products', [
            'name' => 'Large Table',
            'price' => 33.97,
            'image' =>  'products/'.$file->hashName()
        ]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_update_user_product_and_check_if_saved_and_redirected_back_to_userproducts_view()
    {
        Storage::fake('products');
        $user = User::factory()->create();
        $this->assertGuest();
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);
        $product = Product::factory()->create();
        $file = UploadedFile::fake()->image('table.jpg');
        $response = $this->from(route('userproducts.edit'))->post(route('userproducts.update', ['id' => $product->id ]), [
            'name' => 'Large Table',
            'price' => 33.97,
            'image' =>  $file
        ]);
        $response->assertValid(['name', 'price', 'image']);
        $response->assertRedirect(route('userproducts.view', ['id' => $user->id]));
        Storage::disk('products')->assertExists('products/'.$file->hashName());
        $this->assertDatabaseHas('products', [
            'name' => 'Large Table',
            'price' => 33.97,
            'image' =>  'products/'.$file->hashName()
        ]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_delete_user_product_and_check_if_saved_and_redirected_back_to_userproducts_view()
    {
        $user = User::factory()->create();
        $this->assertGuest();
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);
        $product = Product::factory()->create();
        $userProduct = UserProduct::create([ 'product_id' => $product->id, 'user_id' => $user->id ]);
        $response = $this->from(route('userproducts.edit'))->delete(route('userproducts.destroy', ['id' => $product->id ]));
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('url', route('userproducts.view', ['id' => $user->id]));
        $this->assertDatabaseMissing('users', [
            'name' => $product->name,
            'price' => $product->price,
            'image' => $product->image
        ]);
        $this->assertAuthenticatedAs($user);
    }
}
