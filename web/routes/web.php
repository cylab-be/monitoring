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

Auth::routes(['register'=>false]);

Route::get("home", function() {
    return redirect(action("OrganizationController@index"));
});

Route::get('app/dashboard', function() {
    return redirect(action("OrganizationController@index"));
})->name('dashboard');

Route::get('app/organizations/{organization}/dashboard', 'OrganizationController@dashboard');

Route::resource('app/organizations', 'OrganizationController');
Route::resource("app/organizations.user", "OrganizationUserController");
Route::resource('app/servers', 'ServerController');