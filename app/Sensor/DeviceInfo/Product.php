<?php

namespace App\Sensor\DeviceInfo;

use App\SensorConfig;
use App\ServerInfo;

use App\Sensor\ServerInfoParser;

/**
 * Description of ServerInfoProduct
 *
 * @author tibo
 */
class Product extends ServerInfoParser
{
    //put your code here
    public function analyzeString(string $string, ServerInfo $info)
    {
        $REGEX = "/^\s*Product Name: (.*)$/m";

        $matches = [];
        preg_match($REGEX, $string, $matches);
        $info->product = $matches[1] ?? "unknown";
    }

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "ServerInfoProduct",
            "system",
            "Use DMI system to show product name"
        );
    }
}
