<?php

namespace App\Sensor\Linux;

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
