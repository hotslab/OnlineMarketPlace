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
Route::get('/', 'App\Http\Controllers\ProductController@index')->name("products.view");
Route::get('product/{id}', 'App\Http\Controllers\ProductController@show')->name("products.show");

// Authentification
Route::get('login', function () { return view('auth.login'); })->name("login.view");
Route::post('login', 'App\Http\Controllers\AuthenticateController@login')->name("login.post");
Route::get('register', function () { return view('auth.register'); })->name("register.view");
Route::post('register', 'App\Http\Controllers\AuthenticateController@register')->name("register.post");
Route::post('logout', 'App\Http\Controllers\AuthenticateController@logout')->name("logout");
Route::post('password/reset', 'App\Http\Controllers\AuthenticateController@passwordReset')->name("password.reset");

Route::middleware(['auth'])->group(function () {

    // Products
    Route::get('user-products', 'App\Http\Controllers\ProductController@userProducts')->name("userproducts.view");
    Route::get('user-product-edit-view', 'App\Http\Controllers\ProductController@userProductEdit')->name("userproducts.edit");
    Route::post('user-product-edit', 'App\Http\Controllers\ProductController@store')->name("userproducts.store");
    Route::post('user-product-edit/{id}', 'App\Http\Controllers\ProductController@update')->name("userproducts.update");
    Route::delete('user-product-edit/{id}', 'App\Http\Controllers\ProductController@destroy')->name("userproduct.destroy");

    // Profile
    Route::get('profile', function () { return view('auth.profile', ['user' => auth()->user()]); })->name("profile.view");
    Route::post('profile', 'App\Http\Controllers\AuthenticateController@profileUpdate')->name("profile.update");
    Route::post('profile/password/reset', 'App\Http\Controllers\AuthenticateController@profilePasswordReset')->name("profile.passwordreset");
});
