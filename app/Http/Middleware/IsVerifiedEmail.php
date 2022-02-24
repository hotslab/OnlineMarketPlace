<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcessEmailVerification;
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
            ProcessEmailVerification::dispatch(Auth::user()->id);
            Auth::logout();
            Session::flush();
            return redirect()->route('login.view')->with('warning', 'You need to confirm your account. We have resent you an activation link, please check your email in all your folders.');
        }
        return $next($request);
    }
}
