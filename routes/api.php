<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', 'PassportController@login')->name('login');
Route::post('/register', 'PassportController@register')->name('register');
Route::middleware('auth:api')->group(function(){
        Route::get('/user', 'PassportController@details');

        Route::get('/location', 'LocationController@index')->name('location.all');
        Route::post('/location', 'LocationController@store')->name('location.store');
        Route::get('/location/{id}/show', 'LocationController@show')->name('location.show');
        Route::put('/location/{id}/update', 'LocationController@update')->name('location.update');
        Route::delete('/location/{id}/delete', 'LocationController@destroy')->name('location.destroy');

        Route::post('/tank', 'TankController@store')->name('tank.store');
        Route::get('/tank/{id}/show', 'TankController@show')->name('tank.show');
        Route::put('/tank/{id}/update', 'TankController@update')->name('tank.update');
        Route::delete('/tank/{id}/delete', 'TankController@destroy')->name('tank.destroy');

        Route::post('/maketransfer', 'TransferController@transfer')->name('make.transfer');

        Route::get('/dailyVolume', 'TransferController@dailyVolume')->name('daily.volume');

        Route::get('volumedaily', 'TransferController@volumedaily')->name('volumedaily');
});

