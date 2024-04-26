<?php

namespace App;

use Carbon\Carbon;

/**
 * Algorithms used to parse server information from string.
 *
 * @author tibo
 */
class ServerInfoParser
{
    public function parseUptime(string $string) : string
    {
        $pieces = explode(' ', $string);
        $uptime = Carbon::now()->subSeconds($pieces[0]);
        return $uptime->diffForHumans(null, true);
    }

    public function parseUUID(string $string) : string
    {
        $REGEX = "/\s*UUID: (.*)/m";
        
        $matches = array();
        preg_match($REGEX, $string, $matches);
        if (! isset($matches[1])) {
            return "unknown";
        }
        return $matches[1];
    }
    
    public function parseCpuinfo(string $string) : array
    {
        // count the number of vCores
        $REGEX = "/^processor	: (.+)$/m";
        $result["threads"] = preg_match_all($REGEX, $string);
        $result["cpu"] = "undefined";
        
        // try to extract the CPU model
        $REGEX = "/^model name	: (.+)$/m";
        $matches = array();
        if (preg_match($REGEX, $string, $matches) === 1) {
            $result["cpu"] = $matches[1];
            return $result;
        }
        
        // for raspberry pi
        $REGEX = '/^Model\s*: (.+)$/m';
        $matches = array();
        if (preg_match($REGEX, $string, $matches) === 1) {
            $result["cpu"] = $matches[1];
            return $result;
        }
        
        return $result;
    }
    
    public function parseMeminfo(string $string)
    {
        $REGEX = "/^MemTotal:\\s+([0-9]+) kB$/m";
        $matches = array();
        preg_match($REGEX, $string, $matches);
        $total = $matches[1];
        return $total;
    }

    public function parseProductName(string $string) : string
    {
        $REGEX = "/^\s*Product Name: (.*)$/m";
        
        $matches = [];
        preg_match($REGEX, $string, $matches);
        if (!isset($matches[1])) {
            return "unkwnown";
        }
        return $matches[1];
    }
    
    
    public function parseManufacturer(string $string) : string
    {
        $REGEX = "/^\s*Manufacturer: (.*)$/m";
        
        $matches = [];
        preg_match($REGEX, $string, $matches);

        if (!isset($matches[1])) {
            return "unkwnown";
        }
        return $matches[1];
    }
    
    public function parseLsb(string $string) : string
    {
        $REGEX = "/^Description:	(.+)$/m";
        $matches = [];
        preg_match($REGEX, $string, $matches);
        return $matches[1];
    }
    
    public function parseAddresses(string $string) : array
    {
        $ifconfig = new \App\Sensor\Ifconfig();
        $interfaces = $ifconfig->parseIfconfig($string);
        $ips = array_map(
            function (Sensor\NetworkInterface $interface) {
                return $interface->address;
            },
            $interfaces
        );

        // remove empty values
        return array_filter($ips, 'strlen');
    }
}
