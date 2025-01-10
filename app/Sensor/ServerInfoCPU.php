<?php

namespace App\Sensor;

use App\SensorConfig;
use App\ServerInfo;

/**
 *
 * @author tibo
 */
class ServerInfoCPU extends ServerInfoParser
{
    public function analyzeString(string $string, ServerInfo $info)
    {
        // count the number of vCores
        $REGEX = "/^processor	: (.+)$/m";
        $cpuinfo = $info->cpuinfo;
        $cpuinfo["threads"] = preg_match_all($REGEX, $string);
        $info->cpuinfo = $cpuinfo;
    }

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "ServerInfoCPU",
            "cpu",
            "Extract CPU threads from /proc/cpuinfo"
        );
    }
}
