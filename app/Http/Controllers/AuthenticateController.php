<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
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
        return redirect()->route('products.view');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('products.view');
    }
}