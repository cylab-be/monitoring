<?php

use Illuminate\Http\Request;
use App\Server;
use App\Record;

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
    
    $record = new Record();
    $record->server_id = $server->id;
    $record->time = time();
    $record->data = $request->all();
    $record->save();

    return "ok";
});

Route::get(
    'sensor/{server}/{token}/memory',
    function (Server $server, string $token) {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $meminfo = new \App\Sensor\MemInfo();
        return [
            "used" => $meminfo->usedMemoryPoints($server->lastRecords1Day()),
            "cached" => $meminfo->cachedMemoryPoints($server->lastRecords1Day()),
            "total" => $server->info()->memoryTotal() / 1000];
    }
);

Route::get(
    'sensor/{server}/{token}/load',
    function (Server $server, string $token) {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $sensor = new \App\Sensor\LoadAvg();
        return [
            "points" => $sensor->loadPoints($server->lastRecords1Day()),
            "max" => $server->info()->cpuinfo()["threads"]];
    }
);

Route::get(
    'sensor/{server}/{token}/ifconfig',
    function (Server $server, string $token) {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $sensor = new \App\Sensor\Ifconfig();
        return $sensor->points($server->lastRecords1Day());
    }
);

Route::get(
    'sensor/{server}/{token}/netstat',
    function (Server $server, string $token) {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $sensor = new \App\Sensor\Netstat();
        return $sensor->points($server->lastRecords1Day());
    }
);
