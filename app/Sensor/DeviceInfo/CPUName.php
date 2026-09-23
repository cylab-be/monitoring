<?php

namespace App\Sensor\DeviceInfo;

use App\SensorConfig;
use App\ServerInfo;

use App\Sensor\ServerInfoParser;

/**
 * Uses DMI info to extract CPU name.
 *
 * @author tibo
 */
class CPUName extends ServerInfoParser
{
    public function analyzeString(string $string, ServerInfo $info)
    {
        $cpuinfo = $info->cpuinfo;

        $REGEX = "/\tVersion: (.*)/m";
        $matches = [];
        if (preg_match($REGEX, $string, $matches) === 1) {
            $cpuinfo["name"] = $matches[1];
        }

        $info->cpuinfo = $cpuinfo;
    }

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "ServerInfoCPUName",
            "cpu_dmi",
            "Extract CPU name from DMI cpu"
        );
    }
}
