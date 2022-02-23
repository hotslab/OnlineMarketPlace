<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\ProcessPaymentEmail;
use App\Models\Purchase;
use Stripe;

class ProcessStripePaymentIntent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $purchaseID, float $amount, bool $isDeposit, bool $secondDeposit)
    {
        $this->purchaseID = $purchaseID;
        $this->amount = $amount;
        $this->isDeposit = $isDeposit;
        $this->secondDeposit = $secondDeposit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $purchase = Purchase::find($this->purchaseID);
        $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
        $customers = $stripe->customers->all(['email' => $purchase->email ]);
        $customer = collect($customers->data)->first();
        $setupIntents = $stripe->setupIntents->all(['customer' => $customer->id ]);
        $setupIntent = collect($setupIntents->data)->filter(function($intent) { return $intent->status == 'succeeded'; })->first();
        $stripe->paymentIntents->create([
            'customer' => $customer->id,
            'amount' => $this->amount * 100,
            'currency' => strtolower($purchase->product->currency),
            'receipt_email' => $purchase->email,
            'description' => $purchase->product->name.' - '.$purchase->product->currency_symbol.' '.$this->amount,
            'payment_method' => $setupIntent->payment_method,
            'metadata' => [
                'purchase_id' => $purchase->id,
                'product_id'  => $purchase->product->id,
                'product_name' => $purchase->product->name,
                'product_price' => $purchase->product->price,
                'paid_amount' => $this->amount,
                'product_currency' => $purchase->product->currency_symbol,
                'customer_email' => $purchase->email
            ],
            'off_session' => true,
            'confirm' => true
        ]);
        if ($this->isDeposit && $this->secondDeposit) {
            $purchase->paid_amount = $purchase->paid_amount + $this->amount;
            $purchase->save();
        }
        ProcessPaymentEmail::dispatch($purchase->id, $this->amount, $isDeposit);
    }
}
