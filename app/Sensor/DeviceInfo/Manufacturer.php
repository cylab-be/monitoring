<?php

namespace App\Sensor\DeviceInfo;

use App\SensorConfig;
use App\ServerInfo;

use App\Sensor\ServerInfoParser;

/**
 * Description of ServerInfoManufacturer
 *
 * @author tibo
 */
class Manufacturer extends ServerInfoParser
{

    public function analyzeString(string $string, ServerInfo $info)
    {
        $REGEX = "/^\s*Manufacturer: (.*)$/m";

        $matches = [];
        preg_match($REGEX, $string, $matches);
        $info->manufacturer = $matches[1] ?? "unknown";
    }

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "ServerInfoManufacturer",
            "system",
            "Use DMI system to extract manufacturer"
        );
    }
}
