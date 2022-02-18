<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Products

// Authentification
Route::get('login', function () { return view('auth.login'); })->name("login.view");
Route::post('login', 'App\Http\Controllers\AuthenticateController@login')->name("login.post");
Route::get('register', function () { return view('auth.register'); })->name("register.view");
Route::post('register', 'App\Http\Controllers\AuthenticateController@register')->name("register.post");
Route::post('logout', 'App\Http\Controllers\AuthenticateController@logout')->name("logout");
Route::get('email-verify/{id}', 'App\Http\Controllers\AuthenticateController@verifyEmail')->name("email.verify");
Route::get('password/forgot', function () { return view('auth.password.forgot'); })->name("password.forgot");
Route::post('password/reset', 'App\Http\Controllers\AuthenticateController@passwordReset')->name("password.reset");
Route::get('password/otp', 'App\Http\Controllers\AuthenticateController@passwordOTP')->name("password.otp");
Route::post('password/confirm', 'App\Http\Controllers\AuthenticateController@confirmPasswordReset')->name("password.confirm");

Route::middleware(['is_verified_email'])->group(function () {
    // Products
    Route::get('/', 'App\Http\Controllers\ProductController@index')->name("products.view");
    Route::get('product/{id}', 'App\Http\Controllers\ProductController@show')->name("products.show");

    // Purchases
    Route::get('checkout/{id}', 'App\Http\Controllers\ProductController@checkout')->name("purchases.checkout");
    Route::post('purchase', 'App\Http\Controllers\ProductController@purchase')->name("purchases.purchase");
    Route::get('confirmation/{id}', 'App\Http\Controllers\ProductController@confirmation')->name("purchases.confirmation");
});

Route::middleware(['auth', 'is_verified_email'])->group(function () {
    // Products
    Route::get('user-products/{id}', 'App\Http\Controllers\ProductController@userProducts')->name("userproducts.view");
    Route::get('user-product-edit-view', 'App\Http\Controllers\ProductController@userProductEdit')->name("userproducts.edit");
    Route::post('user-product-edit', 'App\Http\Controllers\ProductController@store')->name("userproducts.store");
    Route::post('user-product-edit/{id}', 'App\Http\Controllers\ProductController@update')->name("userproducts.update");
    Route::delete('user-product-edit/{id}', 'App\Http\Controllers\ProductController@destroy')->name("userproduct.destroy");

    // Purchases
    Route::get('purchases/{id}', 'App\Http\Controllers\ProductController@userPurchases')->name("purchases.view");

    // Profile
    Route::get('profile', function () { return view('auth.profile', ['user' => auth()->user()]); })->name("profile.view");
    Route::post('profile', 'App\Http\Controllers\AuthenticateController@profileUpdate')->name("profile.update");
    Route::post('profile/password/reset', 'App\Http\Controllers\AuthenticateController@profilePasswordReset')->name("profile.passwordreset");
});
