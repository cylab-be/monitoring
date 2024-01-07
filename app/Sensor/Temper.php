<?php

namespace App\Sensor;

/**
 * Description of Temper
 *
 * @author helha
 */
class Temper
{
    // allows to extract device response
    // 80 80 09 47 4e 20 00 00
    const REGEX = "/^80\s80\s([0-9a-fA-F]{2}\s[0-9a-fA-F]{2})/m";
    
    public function convert(string $string) : float
    {
        // extract 2 hex values from device response
        // 09 47
        $values = [];
        preg_match(self::REGEX, $string, $values);
        
        // remove intermediate white space
        // 0947
        $hexatemp = preg_replace("/\s+/", "", $values[1]);
        
        // convert to decimal
        // 2375
        $decitemp = hexdec($hexatemp);
        
        return $decitemp / 100.0;
    }
}
