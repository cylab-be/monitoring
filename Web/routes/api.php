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
Route::get('sensors', 'SensorController@index');
Route::get('sensors/{sensor}', 'SensorController@show');
Route::post('sensors', 'SensorController@store');
Route::put('sensors/{sensor}', 'SensorController@update');
Route::delete('sensors/{article}', 'SensorController@delete');
Route::get('register', 'ServerController@register');