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
Route::get('products/{id}', 'App\Http\Controllers\ProductController@show')->name("products.show");

// Authentification
Route::get('login', function () { return view('auth.login'); })->name("login.view");
Route::post('login', 'App\Http\Controllers\AuthenticateController@login')->name("login.post");
Route::get('register', function () { return view('auth.register'); })->name("register.view");
Route::post('register', 'App\Http\Controllers\AuthenticateController@register')->name("register.post");
Route::post('logout', 'App\Http\Controllers\AuthenticateController@logout')->name("logout");

Route::middleware(['auth'])->group(function () {

    // Products
    Route::post('products', 'App\Http\Controllers\ProductController@store')->name("products.store");
    Route::patch('products/{id}', 'App\Http\Controllers\ProductController@update')->name("products.update");
    Route::delete('products/{id}', 'App\Http\Controllers\ProductController@destroy')->name("products.destroy");

    // Profile
    Route::get('profile', function () { return view('user.profile'); })->name("profile.view");
    Route::post('profile', 'App\Http\Controllers\UserController@update')->name("products.update");
    Route::post('user-products', 'App\Http\Controllers\UserController@update')->name("products.update");
});
