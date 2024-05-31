<?php

namespace App\Sensor;

use App\SensorConfig;
use App\ServerInfo;

/**
 * Description of ServerInfoCPU
 *
 * @author tibo
 */
class ServerInfoCPU extends ServerInfoParser
{
    public function analyzeString(string $string, ServerInfo $info)
    {
        // count the number of vCores
        $REGEX = "/^processor	: (.+)$/m";
        $result["threads"] = preg_match_all($REGEX, $string);
        $result["name"] = "undefined";
        
        // try to extract the CPU model
        $REGEX = "/^model name	: (.+)$/m";
        $matches = array();
        if (preg_match($REGEX, $string, $matches) === 1) {
            $result["name"] = $matches[1];
            $info->cpuinfo = $result;
            return;
        }
        
        // for raspberry pi
        $REGEX = '/^Model\s*: (.+)$/m';
        $matches = array();
        if (preg_match($REGEX, $string, $matches) === 1) {
            $result["name"] = $matches[1];
            $info->cpuinfo =  $result;
            return;
        }
    }

    public function config(): SensorConfig
    {
        return new SensorConfig("", "cpu");
    }
}
