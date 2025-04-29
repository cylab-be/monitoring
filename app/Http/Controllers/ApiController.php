<?php

namespace App\Http\Controllers;

use App\Server;
use App\Record;

use Illuminate\Http\Request;

class ApiController extends Controller
{

    public function echo(Request $request, Server $server)
    {
        if ($server->token !== $request->get("token", "")) {
            abort(403);
        }

        foreach ($request->all() as $label => $data) {
            if (is_null($data)) {
                continue;
            }

            $record = new Record();
            $record->server_id = $server->id;
            $record->time = time();
            $record->label = $label;
            $record->data = $data;
            $record->save();
        }

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
            "used" => $meminfo->usedMemoryPoints($server->lastRecords("memory")),
            "cached" => $meminfo->cachedMemoryPoints($server->lastRecords("memory")),
            "total" => $server->info->memory / 1000];
    }

    public function load(Server $server, string $token)
    {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $sensor = new \App\Sensor\LoadAvg();
        return [
            "points" => $sensor->loadPoints($server->lastRecords("loadavg")),
            "max" => $server->info->vCores()];
    }

    public function ifconfig(Server $server, string $token)
    {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $sensor = new \App\Sensor\Ifconfig();
        return $sensor->points($server->lastRecords("ifconfig"));
    }

    public function netstat(Server $server, string $token)
    {
        if ($server->read_token != $token) {
            abort(403);
        }

        header('Access-Control-Allow-Origin: *');
        $sensor = new \App\Sensor\Netstat();
        return $sensor->points($server->lastRecords("netstat-statistics"));
    }
}
