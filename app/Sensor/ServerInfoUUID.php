<?php

namespace App\Sensor;

use App\SensorConfig;
use App\ServerInfo;

/**
 * Description of ServerInfoUUID
 *
 * @author tibo
 */
class ServerInfoUUID extends ServerInfoParser
{
    public function config(): SensorConfig
    {
        return new SensorConfig(
                "ServerInfoUUID",
                "system",
                "Use DMI system to extract system UUID");
    }

    public function analyzeString(string $string, ServerInfo $info)
    {
        $REGEX = "/\s*UUID: (.*)/m";

        $matches = array();
        preg_match($REGEX, $string, $matches);
        $info->uuid =  $matches[1] ?? "unknown";
    }
}
