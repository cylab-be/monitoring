<?php

namespace App\Sensor;

/**
 * Time point that can be fed to graph.js
 *
 * @author tibo
 */

class Point
{
    public $t = 0;
    public $y = 0;

    /**
     *
     * @param int $time in milliseconds
     * @param float|int $value
     */
    public function __construct(int $time, $value)
    {
        $this->t = $time;
        $this->y = $value;
    }
}
