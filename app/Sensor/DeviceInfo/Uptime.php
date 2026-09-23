<?php

namespace App\Sensor\DeviceInfo;

use App\SensorConfig;
use App\ServerInfo;

use App\Sensor\ServerInfoParser;

/**
 * Description of ServerInfoUptime
 *
 * @author tibo
 */
class Uptime extends ServerInfoParser
{
    public function analyzeString(string $string, ServerInfo $info)
    {
        $pieces = explode(' ', $string);
        $info->uptime = intval($pieces[0]);
    }


    public function config(): SensorConfig
    {
        return new SensorConfig(
            "ServerInfoUptime",
            "upaimte",
            "Parse /proc/uptime"
        );
    }
}
