<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class ProcessEmailVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userID;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $userID)
    {
        $this->userID = $userID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::find($this->userID);
        $response = Http::get('https://api.elasticemail.com/v2/email/send', [
            'apikey' => env('ELASTIC_EMAIL_API'),
            'subject' => 'Email verificaton for '.$user->name.' '.$user->surname,
            'from' => env('ELASTIC_EMAIL_SENDER_ADDRESS'),
            'to' => $user->email,
            'bodyHtml' => 
                '<div style="text-align:center; padding: 30px;">'.
                    '<h2 style="color:#1E90FF;">Wecome to the OnlineMarketPlace</h2>'.
                    '<p>Please verify your email by clicking the link below in order to activate your account. The you can start adding and selling products online:</p>'.
                    '<a href="'.route('email.verify', ['id' => $user->id]).'" style="text-decoration: none;cursor: pointer;">Verify Email</a>'.
                    '<p style="margin-top: 30px">&copy; OnlineMarketPlace 2022</p>'.
                '</div>'
        ]);
    }
}
