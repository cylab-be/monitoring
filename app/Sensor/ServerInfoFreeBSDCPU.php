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
        $REGEX = "/^\tThread Count: (\d+)$/m";

        $count = 0;
        $matches = [];
        $cpuinfo = $info->cpuinfo;

        preg_match_all($REGEX, $string, $matches);

        foreach ($matches[1] as $match) {
            $count += $match[0];
        }
        
        // if regex does not match, or dmi info is empty...
        if ($count == 0) {
            return;
        }

        $cpuinfo["threads"] = $count;
        $info->cpuinfo = $cpuinfo;
    }

    public function config(): SensorConfig
    {
        return new SensorConfig("ServerInfoFreeBSDCPU", "cpu-dmi");
    }
}
