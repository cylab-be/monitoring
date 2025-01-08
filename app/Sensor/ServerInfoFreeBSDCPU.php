<?php

namespace App\Sensor;

use App\SensorConfig;
use App\ServerInfo;

/**
 * Description of ServerInfoCPU
 *
 * @author tibo
 */
class ServerInfoFreeBSDCPU extends ServerInfoParser
{
    public function analyzeString(string $string, ServerInfo $info)
    {
        // count the number of vCores
        $REGEX = "/^	Thread Count: (\d+)/m";

        $count = 0;
        $matches = [];
        $cpuinfo = $info->cpuinfo;

        preg_match_all($REGEX, $string, $matches);

        var_dump($matches);

        foreach ($matches[1] as $match) {
            $count += $match[0];
        }

        $cpuinfo["threads"] = $count;
        $info->cpuinfo = $cpuinfo;
    }

    public function config(): SensorConfig
    {
        return new SensorConfig("ServerInfoFreeBSDCPU", "cpu-dmi");
    }
}
