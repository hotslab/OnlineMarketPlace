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
Route::get('/', 'App\Http\Controllers\ProductController@index');
Route::get('products/{id}', 'App\Http\Controllers\ProductController@show');

Route::post('login', 'App\Http\Controllers\AuthenticateController@authenticate');
Route::post('register', 'App\Http\Controllers\AuthenticateController@authenticate');
Route::post('logout', 'App\Http\Controllers\AuthenticateController@logout');

Route::middleware(['auth'])->group(function () {
    Route::post('products', 'App\Http\Controllers\ProductController@store');
    Route::patch('products/{id}', 'App\Http\Controllers\ProductController@update');
    Route::delete('products/{id}', 'App\Http\Controllers\ProductController@destroy');
});
