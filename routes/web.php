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
// maintain old URL convention
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


        Route::resource('organizations', 'OrganizationController');
        Route::get('organizations/{organization}/dashboard', 'OrganizationController@dashboard')
                ->name("organizations.dashboard");
        Route::get('organizations/{organization}/reset-token', 'OrganizationController@resetToken')
                ->name("organizations.reset-token");


        // ansible inventory
        Route::get("organizations/{organization}/inventory", "OrganizationController@inventory")
                ->name("organizations.inventory");


        // users
        Route::resource("organizations/{organization}/users", "OrganizationUserController")
                ->only(["create", "store", "destroy"]);

        // devices
        Route::get("organizations/{organization}/servers", "ServerController@index")->name("servers.index");
        Route::get("organizations/{organization}/servers/create", "ServerController@create")->name("servers.create");

        // device - tags
        Route::post("servers/{server}/tags", "ServerController@addTag")->name("servers.tags.add");
        Route::delete("servers/{server}/tags/{tag}", "ServerController@deleteTag")->name("servers.tags.remove");

        Route::get("servers/{server}/records", "ServerController@records")->name("servers.records");
        Route::resource('servers', 'ServerController')->except(["index", "create"]);

        // manual IP addresses
        Route::get("servers/{server}/ips/create", "IpController@create")->name("ips.create");
        Route::post("ips", "IpController@store")->name("ips.store");
        Route::delete("ips/{ip}", "IpController@destroy")->name("ips.destroy");

        // device records
        Route::get("/records/{record}", "RecordController@show")->name("records.show");
        Route::get("/records/{record}/{agent}", "RecordController@run")->name("records.run");

        // racks
        Route::get("organizations/{organization}/racks", 'RackController@index')->name("racks.index");
        Route::get("organizations/{organization}/racks/create", 'RackController@create')->name("racks.create");
        Route::get("organizations/{organization}/racks/dashboard", 'RackController@dashboard')->name("racks.dashboard");
        Route::resource("racks", 'RackController')->except(["index", "create"]);

        // subnets / IPAM
        Route::get("organizations/{organization}/subnets", 'SubnetController@index')->name("subnets.index");
        Route::get("organizations/{organization}/subnets/create", 'SubnetController@create')->name("subnets.create");
        Route::get("organizations/{organization}/subnets/view", "SubnetController@view")->name("subnets.view");
        Route::resource("subnets", 'SubnetController')->except(["index", "create"]);

        // tags
        Route::get("organizations/{organization}/tags", 'TagController@index')->name("tags.index");
        Route::get("organizations/{organization}/tags/create", 'TagController@create')->name("tags.create");
        Route::get("organizations/{organization}/tags/view", "TagController@view")->name("tags.view");
        Route::resource("tags", 'TagController')->except(["index", "create"]);

        // insights
        Route::get("organizations/{organization}/insights/packages", 'InsightsController@packages')
                ->name("insights.packages");
        Route::get("organizations/{organization}/insights/stacks", 'InsightsController@stacks')
                ->name("insights.stacks");
    });
