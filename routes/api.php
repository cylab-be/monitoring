<?php

use Illuminate\Http\Request;
use App\Server;

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

Route::post('record/{server}', function (Request $request, Server $server) {
    if ($server->token !== $request->get("token", "")) {
        abort(403);
    }

    $data = $request->all();
    $data["server_id"] = $server->id;
    $data["time"] = time();

    $collection = Mongo::get()->monitoring->records;
    $collection->insertOne($data);

    return "ok";
});

Route::get(
    'sensor/{server}/{token}/memory',
    function (Server $server, string $token) {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $meminfo = new App\Sensor\MemInfo($server);
        return [
            "used" => $meminfo->usedMemoryPoints(),
            "cached" => $meminfo->cachedMemoryPoints(),
            "total" => $server->memoryTotal() / 1000];
    }
);

Route::get(
    'sensor/{server}/{token}/load',
    function (Server $server, string $token) {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $sensor = new App\Sensor\LoadAvg($server);
        return [
            "points" => $sensor->loadPoints(),
            "max" => $server->cpuinfo()["threads"]];
    }
);

Route::get(
    'sensor/{server}/{token}/ifconfig',
    function (Server $server, string $token) {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $sensor = new App\Sensor\Ifconfig($server);
        return $sensor->points();
    }
);

Route::get(
    'sensor/{server}/{token}/netstat',
    function (Server $server, string $token) {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $sensor = new App\Sensor\Netstat($server);
        return $sensor->points();
    }
);