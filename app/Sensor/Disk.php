<?php

namespace App\Sensor;

use App\Status;
use App\HasStatus;

/**
 * Description of Disk
 *
 * @author tibo
 */
class Disk implements HasStatus
{
    public $port = "";
    public $box = 0;
    public $bay = 0;
    public $type = "";
    public $size = "";
    
    /**
     *
     * @var \App\Status
     */
    public $status;
    
    public function status() : Status
    {
        return $this->status;
    }
}
