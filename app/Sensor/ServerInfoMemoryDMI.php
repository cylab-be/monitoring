<?php

namespace App\Sensor;

use App\SensorConfig;
use App\ServerInfo;

/**
 * Uses DMI to extract total memory.
 *
 * @author tibo
 */
class ServerInfoMemoryDMI extends ServerInfoParser
{
    //put your code here
    public function analyzeString(string $string, ServerInfo $info)
    {
        $REGEX = '/^\tSize: (\d+) GB/m';
        
        $total = 0;
        $matches = [];
        
        preg_match_all($REGEX, $string, $matches);

        foreach ($matches[1] as $match) {
            $total += $match;
        }
        
        // if regex does not match or dmi info is empty...
        if ($total == 0) {
            return;
        }
        
        $info->memory = $total * 1024 * 1024;
    }

    public function config(): SensorConfig
    {
        return new SensorConfig("ServerInfoMemoryDMI", "memory-dmi");
    }
}
