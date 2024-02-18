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

// https://cylab.be/blog/122/using-https-over-a-reverse-proxy-in-laravel
$app_url = config("app.url");
if (app()->environment('prod') && !empty($app_url)) {
    $schema = explode(':', $app_url)[0];
    URL::forceRootUrl($app_url);
    URL::forceScheme($schema);
}


Route::get('/', function () {
    return view('index');
});

Auth::routes(['register' => config("app.allow_registration")]);

Route::get("home", function () {
    return redirect(action("OrganizationController@index"));
});

Route::get('app/dashboard', function () {
    return redirect(action("OrganizationController@index"));
})->name('dashboard');

Route::get('app/organizations/{organization}/dashboard', 'OrganizationController@dashboard');
Route::get(
    'app/organizations/{organization}/reset-token',
    'OrganizationController@resetToken'
);

// public dashboard
Route::get(
    'app/organizations/{organization}/dashboard/{token}',
    'OrganizationDashboardController@dashboard'
)->name("organization.public.dashboard");

// public json dashboard
Route::get(
    'app/organizations/{organization}/{token}/dashboard.json',
    'OrganizationDashboardController@json'
);

Route::resource('app/organizations', 'OrganizationController');
Route::resource("app/organizations.user", "OrganizationUserController")->only(["create", "store", "destroy"]);
Route::resource('app/servers', 'ServerController')->except(["index"]);
Route::get("app/records/{record}", "RecordController@show");
