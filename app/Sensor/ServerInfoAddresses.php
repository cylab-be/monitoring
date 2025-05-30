<?php

namespace App\Sensor;

use App\SensorConfig;
use App\ServerInfo;

/**
 * Prase ifconfig agent to get IP addresses of server.
 *
 * @author tibo
 */
class ServerInfoAddresses extends ServerInfoParser
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
