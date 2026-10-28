<?php

namespace App\Sensor\Linux\Debian;

/**
 * Description of ListPackages
 *
 * @author tibo
 */
class ListPackages
{
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
