<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Purchase;


class ProcessPaymentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $purchaseID;
    protected $paidAmount;
    protected $isDeposit;
    protected $secondDeposit;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $purchaseID, float $paidAmount, bool $isDeposit, bool $secondDeposit)
    {
        $this->purchaseID = $purchaseID;
        $this->paidAmount = $paidAmount;
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
        $subject = 'New product purchased: '.$purchase->product->name.' - '.$purchase->product->currency_symbol.' '.$this->paidAmount;
        if ($this->isDeposit) {
            $subject = ($this->secondDeposit ? 'Final' : 'First' ).' deposit payment for product purchased: '.$purchase->product->name.' - '.$purchase->product->currency_symbol.' '.$this->paidAmount;
        }
        Http::get('https://api.elasticemail.com/v2/email/send', [
            'apikey' => env('ELASTIC_EMAIL_API'),
            'subject' => $subject,
            'from' => env('ELASTIC_EMAIL_SENDER_ADDRESS'),
            'to' => $purchase->email,
            'bodyHtml' => 
                '<div style="text-align:center; padding: 30px;">'.
                    '<h2 style="color:#1E90FF;">Thank you for shopping at the OnlineStore</h2>'.
                    '<p>You have successfully purchased the following product at our online store:</p>'.
                    '<table style="font-family: Arial, Helvetica, sans-serif;border-collapse: collapse;width: 100%; margin-top: 20px;">'.
                        '<thead style="padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #1E90FF;color: white;">'.
                            '<tr>'.
                                '<th style="border:1px solid #ddd;padding: 8px;">Name</th>'.
                                '<th style="border:1px solid #ddd;padding: 8px;">Full Amount</th>'.
                                '<th style="border:1px solid #ddd;padding: 8px;">Deposit Amount</th>'.
                                '<th style="border:1px solid #ddd;padding: 8px;">Quantity (Units)</th>'.
                                '<th style="border:1px solid #ddd;padding: 8px;">Deposit Payment</th>'.
                            '</tr>'.
                        '</thead>'.
                        '<tbody style="text-align: left;">'.
                            '<tr>'.
                                '<td style="border:1px solid #ddd;padding: 8px;">'.$purchase->product->name.'</td>'.
                                '<td style="border:1px solid #ddd;padding: 8px;">'.$purchase->product->currency_symbol.' '.$purchase->product->price.'</td>'.
                                '<td style="border:1px solid #ddd;padding: 8px;">'.$purchase->product->currency_symbol.' '.$this->paidAmount.'</td>'.
                                '<td style="border:1px solid #ddd;padding: 8px;">1</td>'.
                                '<td style="border:1px solid #ddd;padding: 8px;">'.($this->isDeposit ? 'true' : 'false').'</td>'.
                            '</tr>'.
                        '</tbody>'.
                    '</table>'.
                    '<p style="margin-top: 30px">&copy; OnlineStore 2022</p>'.
                '</div>'
        ]);
    }
}
