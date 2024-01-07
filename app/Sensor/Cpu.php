<?php

namespace App\Sensor;

use App\Status;

/**
 * Description of Cpu
 *
 * @author helha
 */
class Cpu extends Core
{

    public $cores = [];

    public function status() : Status
    {
        return max(
            Status::max($this->cores),
            parent::status()
        );
    }
}
