<?php

namespace App\Sensor\DeviceInfo;

use App\SensorConfig;
use App\ServerInfo;

use App\Sensor\ServerInfoParser;

/**
 * Description of ServerInfoLSB
 *
 * @author tibo
 */
class DistroLSB extends ServerInfoParser
{
    //put your code here
    public function analyzeString(string $string, ServerInfo $info)
    {
        $REGEX = "/^Description:	(.+)$/m";
        $matches = [];
        preg_match($REGEX, $string, $matches);
        $info->lsb = $matches[1] ?? "unknown";
    }

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "ServerInfoLSB",
            "lsb",
            "Show distribution name using lsb_info"
        );
    }
}
