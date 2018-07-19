<?php

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

Route::get('/', function () {
    return view('index');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/org', 'OrganizationController@index')->name('organizations');
Route::post('/org', 'OrganizationController@addOrg');
Route::post('/org/{name}', 'OrganizationController@addServer');
Route::get('/org/{name}', ['uses' =>'OrganizationController@details'])->name('organization');
Route::get('/server/{id}', ['uses' =>'ServerController@server'])->name('serverDetails');
