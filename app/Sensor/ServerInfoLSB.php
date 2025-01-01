<?php

namespace App\Sensor;

use App\SensorConfig;
use App\ServerInfo;

/**
 * Description of ServerInfoLSB
 *
 * @author tibo
 */
class ServerInfoLSB extends ServerInfoParser
{
    //put your code here
    public function analyzeString(string $string, ServerInfo $info)
    {
        $REGEX = "/^Description:	(.+)$/m";
        $matches = [];
        preg_match($REGEX, $string, $matches);
        $info->lsb = $matches[1] ?? "unknown";
    }

    public function config(): SensorConfig
    {
        return new SensorConfig("ServerInfoLSB", "lsb");
    }
}
