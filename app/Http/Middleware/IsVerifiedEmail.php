<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Session;

class IsVerifiedEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user() && !Auth::user()->email_verified_at) {
            $response = Http::get('https://api.elasticemail.com/v2/email/send', [
                'apikey' => env('ELASTIC_EMAIL_API'),
                'subject' => 'Email verificaton for '.Auth::user()->name.' '.Auth::user()->surname,
                'from' => env('ELASTIC_EMAIL_SENDER_ADDRESS'),
                'to' => Auth::user()->email,
                'bodyHtml' => 
                    '<div style="text-align:center;">'.
                        '<h2 style="color:#1E90FF;">Wecome to the OnlineStore</h2>'.
                        '<p>Please verify your email by clicking the link below in order to activate your account. The you can start adding and selling products online:</p>'.
                        '<a href="'.route('email.verify', ['id' => Auth::user()->id]).'" style="text-decoration: none;cursor: pointer;">Verify Email</a>'.
                        '<p style="margin-top: 30px">&copy; OnlineStore 2022</p>'.
                    '</div>'
            ]);
            Auth::logout();
            Session::flush();
            return redirect()->route('login.view')->with('warning', 'You need to confirm your account. We have resent you an activation link, please check your email in all your folders.');
        }
        return $next($request);
    }
}
