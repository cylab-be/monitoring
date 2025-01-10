<?php

namespace App\Sensor;

use App\SensorConfig;
use App\ServerInfo;

/**
 * Description of ServerInfoAddresses
 *
 * @author tibo
 */
class ServerInfoAddresses extends ServerInfoParser
{
    //put your code here
    public function analyzeString(string $string, ServerInfo $info)
    {
        $ifconfig = new Ifconfig();
        $interfaces = $ifconfig->parseIfconfig($string);
        $ips = array_map(
            function (NetworkInterface $interface) {
                return $interface->address;
            },
            $interfaces
        );

        // remove empty values
        $info->addresses = array_filter($ips, 'strlen');
    }

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "ServerInfoAddresses",
            "ifconfig",
            "Parse ifconfig command to extract IPv4 addresses of the server"
        );
    }
}
