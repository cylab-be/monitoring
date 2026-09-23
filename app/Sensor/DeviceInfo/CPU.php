<?php

namespace App\Sensor\DeviceInfo;

use App\SensorConfig;
use App\ServerInfo;

use App\Sensor\ServerInfoParser;

/**
 *
 * @author tibo
 */
class CPU extends ServerInfoParser
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
            "Extract number CPU threads from /proc/cpuinfo"
        );
    }
}
