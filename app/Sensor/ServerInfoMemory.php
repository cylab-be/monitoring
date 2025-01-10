<?php

namespace App\Sensor;

use App\SensorConfig;
use App\ServerInfo;

/**
 * Description of ServerInfoMemory
 *
 * @author tibo
 */
class ServerInfoMemory extends ServerInfoParser
{
    //put your code here
    public function analyzeString(string $string, ServerInfo $info)
    {
        $REGEX = "/^MemTotal:\\s+([0-9]+) kB$/m";
        $matches = array();
        preg_match($REGEX, $string, $matches);
        $info->memory = $matches[1];
    }

    public function config(): SensorConfig
    {
        return new SensorConfig(
                "ServerInfoMemory",
                "memory",
                "Parses /proc/meminfo to extract total available memory");
    }
}
