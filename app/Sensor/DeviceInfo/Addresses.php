<?php

namespace App\Sensor\DeviceInfo;

use App\SensorConfig;
use App\ServerInfo;
use App\Sensor\ServerInfoParser;
use App\Sensor\Linux\Ifconfig;

/**
 * Prase ifconfig agent to get IP addresses of server.
 *
 * @author tibo
 */
class Addresses extends ServerInfoParser
{
    public function analyzeString(string $string, ServerInfo $info)
    {
        $ifconfig = new Ifconfig();
        $interfaces = $ifconfig->parseIfconfig($string);
        $ips = [];

        foreach ($interfaces as $interface) {
            foreach ($interface->addresses as $address) {
                $ips[] = $address;
            }
        }

        // remove empty values
        $info->addresses = array_filter(
            $ips,
            fn($address) => strlen($address) != 0
        );
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
