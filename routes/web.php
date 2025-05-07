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
    // already logged in
    if (auth()->user()) {
        return redirect(route("organizations.index"));
    }
    return view('index');
});

Auth::routes(['register' => config("app.allow_registration")]);

// public json dashboard
Route::get(
    '/app/organizations/{organization}/{token}/dashboard.json',
    'OrganizationDashboardController@json'
)->name("organizations.json");

Route::middleware(['auth'])
    ->group(function () {
        Route::get("/home", function () {
            return redirect(route("organizations.index"));
        })->name("home");

        Route::get("/status", function () {
            return view("status");
        })->name("status");


        Route::resource('/organizations', 'OrganizationController');
        Route::get("/organizations/{organization}/select", 'OrganizationController@select')
                ->name("organizations.select");

        Route::get('/organizations/{organization}/reset-token', 'OrganizationController@resetToken')
                ->name("organizations.reset-token");

        Route::resource("/organizations.users", "OrganizationUserController")->only(["create", "store", "destroy"]);

        // For all routes and resources below, an organization must be selected
        // and stored in the session
        Route::get('/dashboard', 'OrganizationController@dashboard')->name("organizations.dashboard");

        Route::resource('/servers', 'ServerController');
        Route::get("/servers/{server}/records", "ServerController@records")->name("servers.records");

        Route::get("/racks/dashboard", 'RackController@dashboard')->name("racks.dashboard");
        Route::resource("/racks", 'RackController');
        
        Route::resource("/subnets", 'SubnetController');

        Route::get("/records/{record}", "RecordController@show")->name("records.show");
    });
