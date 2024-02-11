<?php

namespace App\Http\Controllers;

use App\Server;
use App\Record;

use Illuminate\Http\Request;

class ApiController extends Controller {

    public function echo(Request $request, Server $server)
    {
        if ($server->token !== $request->get("token", "")) {
            abort(403);
        }

        $record = new Record();
        $record->server_id = $server->id;
        $record->time = time();
        $record->data = $request->all();
        $record->save();

        return "ok";
    }

    public function memory(Server $server, string $token)
    {
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

    public function load(Server $server, string $token)
    {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $sensor = new \App\Sensor\LoadAvg();
        return [
            "points" => $sensor->loadPoints($server->lastRecords1Day()),
            "max" => $server->info()->cpuinfo()["threads"]];
    }
    
    public function ifconfig (Server $server, string $token)
    {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $sensor = new \App\Sensor\Ifconfig();
        return $sensor->points($server->lastRecords1Day());
    }
    
    public function netstat (Server $server, string $token)
    {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $sensor = new \App\Sensor\Netstat();
        return $sensor->points($server->lastRecords1Day());
    }
}
