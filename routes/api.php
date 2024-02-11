<?php

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

Route::post('record/{server}', "ApiController@echo");

Route::get('sensor/{server}/{token}/memory', "ApiController@memory");
Route::get('sensor/{server}/{token}/load', "ApiController@load");
Route::get('sensor/{server}/{token}/ifconfig', "ApiController@ifconfig");
Route::get('sensor/{server}/{token}/netstat', "ApiController@netstat");
