<?php

namespace App\Sensor\DeviceInfo;

use App\SensorConfig;
use App\ServerInfo;

use App\Sensor\ServerInfoParser;

/**
 * Use uname to get kernel name and version
 *
 * @author tibo
 */
class Kernel extends ServerInfoParser
{
    #[\Override]
    public function analyzeString(string $string, ServerInfo $info)
    {
        $info->kernel = $string;
    }

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "ServerInfoKernel",
            "uname",
            "Show kernel version using uname"
        );
    }
}
