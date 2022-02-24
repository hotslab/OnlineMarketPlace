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

class ProcessPasswordResetEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userID;
    protected $token;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $userID, string $token)
    {
        $this->userID = $userID;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::find($this->userID);
        Http::get('https://api.elasticemail.com/v2/email/send', [
            'apikey' => env('ELASTIC_EMAIL_API'),
            'subject' => 'Password reset for '.$user->name.' '.$user->surname,
            'from' => env('ELASTIC_EMAIL_SENDER_ADDRESS'),
            'to' => $user->email,
            'bodyHtml' => 
                '<div style="text-align:center; padding: 30px;">'.
                    '<h2 style="color:#1E90FF;">Reset your password for the OnlineMarketPlace</h2>'.
                    '<p>Please enter the otp below in the password reset screen to reset your password :</p>'.
                    '<h4 style="color:#1E90FF;">'.$this->token.'</h4>'.
                    '<p style="margin-top: 30px">&copy; OnlineMarketPlace 2022</p>'.
                '</div>'
        ]);
    }
}
