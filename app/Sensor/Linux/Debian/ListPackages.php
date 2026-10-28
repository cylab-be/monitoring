<?php

namespace App\Sensor\Linux\Debian;

use App\SensorConfig;

/**
 * Description of ListPackages
 *
 * @author tibo
 */
class ListPackages
{
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "apt-list-packages",
            "apt_list",
            "List installed APT packages"
        );
    }
    
    /**
     * Return an array of packages
     * @param string $data
     * @return array
     */
    public function parse(string $data) : array
    {
        // skip first line as it only contains
        // "Listing..."
        return array_slice(explode("\n", $data), 1);
    }
}
