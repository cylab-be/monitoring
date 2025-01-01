<?php

namespace App\Sensor;

use App\SensorConfig;
use App\ServerInfo;

/**
 * Description of ServerInfoUptime
 *
 * @author tibo
 */
class ServerInfoUptime extends ServerInfoParser
{
    public function analyzeString(string $string, ServerInfo $info)
    {
        $pieces = explode(' ', $string);
        $info->uptime = intval($pieces[0]);
    }


    public function config(): SensorConfig
    {
        return new SensorConfig("ServerInfoUptime", "upaimte");
    }
}
