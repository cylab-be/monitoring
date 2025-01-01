<?php

namespace App\Sensor;

use App\SensorConfig;
use App\ServerInfo;

/**
 * Use uname to get kernel name and version
 *
 * @author tibo
 */
class ServerInfoKernel extends ServerInfoParser
{
    public function analyzeString(string $string, ServerInfo $info)
    {
        $info->kernel = $string;
    }

    public function config(): SensorConfig
    {
        return new SensorConfig("ServerInfoKernel", "uname");
    }
}
