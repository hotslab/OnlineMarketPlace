<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Jobs\ProcessPasswordResetEmail;
use App\Jobs\ProcessEmailVerification;
use App\Models\PasswordReset;
use App\Models\User;
 
class AuthenticateController extends Controller
{
    public function login(Request $request)
    {
        $credentials = ["email" => $request->input("email"), "password" => $request->input("password")]; 
        $validator = Validator::make($credentials, ['email' => ['required', 'email'], 'password' => ['required']]);
        if ($validator->fails()) { return back()->withErrors($validator)->withInput(); }
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('products.view');
        } else {
            return back()->with('failure', 'Incorrect credentials used. Please try again.');
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);
        if ($validator->fails()) { return back()->withErrors($validator)->withInput(); } 
        $newUser = User::create([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        if ($newUser) {
            try {
                ProcessEmailVerification::dispatchAfterResponse($newUser->id);
                return redirect()->route('login.view')->with(
                    'success', 
                    'Registration was successful. We have sent you an activation link to '.$newUser->email.', please check in all your folders for the link.'
                );
            } catch (\Throwable $th) {
                return back()->with('failure', 'Verification email could not be sent to '.$newUser->email.' due to : '.$th->getMessage);        
            }
        } else {
            back()->with('failure', 'Profile could not be created. Please try again.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('products.view');
    }

    public function profileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(Auth::user()->id)]
        ]);
        if ($validator->fails()) { return back()->withErrors($validator)->withInput(); } 
        $newUser = Auth::user()
        ->update([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'email' => $request->input('email')
        ]);
        return back()->with('success', 'Profile details updated successfully.');
    }

    public function profilePasswordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);
        if ($validator->fails()) { return back()->withErrors($validator)->withInput(); } 
        Auth::user()->update(['password' => Hash::make($request->input('password'))]);
        return back()->with('success', 'Password reset successfully.');
    }

    public function verifyEmail($id)
    {
        User::find($id)->update(['email_verified_at' => date('Y-m-d H:i:s')]);
        return redirect()->route('login.view')->with('success', 'Your email has been successfully verified. Please login to start enjoying the use of our services.');
    }

    public function passwordReset(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => ['required', 'string', 'email', 'max:255']]);
        if ($validator->fails()) { return back()->withErrors($validator)->withInput(); } 
        $userExists = User::where('email', $request->input('email'))->first();
        if ($userExists) {
            $token = Str::random(4);
            $paswwordReset = PasswordReset::updateOrInsert(
                ['email' => $request->input('email')],
                ['token' => $token]
            );
            if ($paswwordReset) {
                try {
                    ProcessPasswordResetEmail::dispatchAfterResponse($userExists->id, $token);
                    return redirect()->route('password.otp', ['id' => $userExists->id]);
                } catch (\Throwable $th) {
                    return back()->with('failure', 'Password reset token could not be sent to  '.$request->input('email').' due to: '.$th->getMessage());        
                }
            } else {
                return back()->with('failure', 'No user with the email '.$request->input('email').' has been found. Please try again.');    
            }
        } else {
            return back()->with('failure', 'No user with the email '.$request->input('email').' has been found. Please try again.');
        }
    }

    public function otpScreen($id)
    {
        return View::make('auth.password.otp', ['user' => User::find($id)]);   
    }

    public function confirmPasswordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'otp' => ['required', 'string', 'max:4']
        ]);
        if ($validator->fails()) { return back()->withErrors($validator)->withInput(); } 
        $user = User::find($request->input("user_id"));
        if ($user) {
            $resetToken = PasswordReset::where("email", $user->email)->where("token", $request->input("otp"))->first();
            if ($resetToken) {
                $user->password = Hash::make($request->input('password'));
                $user->save();
                return redirect()->route('login.view')->with('success', 'The password for '.$user->email.' was successfully reset.');
            } else {
                return back()->with('failure', 'Reset token for '.$user->email.' not found due to unknown error. Please restart the password reset process.');           
            }
        } else {
            return back()->with('failure', 'User with email address '.$user->email.' not found due to unknown error. Please restart the password reset process.');           
        }
    }
}