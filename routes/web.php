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

Route::group(['prefix' => 'api'], function () {
    Route::post('login', 'Auth\LoginController@login')->name("system.auth.login.post");
    Route::get('ping', function (Request $request) {
        return "pong";
    });
    Route::group(['middleware' => 'auth'], function() {});
});
/* Route::get('/api/pong', function (Request $request) {
    return "ping";
}); */


Route::get(
    '{uri}',
    '\\'.Pallares\LaravelNuxt\Controllers\NuxtController::class
)->where('uri', '.*');
