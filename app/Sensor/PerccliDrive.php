<?php

namespace App\Sensor;

use App\Status;
use App\HasStatus;

/**
 * Represents a single physical drive connected to a Dell RAID controller.
 *
 * @author tibo
 */
class PerccliDrive implements HasStatus
{
    public $slot;
    
    /**
     *
     * @var \App\Status
     */
    public $status;
    public $size;
    public $type;
    
    public function status() : Status
    {
        return $this->status;
    }
}
