<?php

namespace App\Sensor;

use App\Status;
use App\HasStatus;

/**
 * Description of Partition
 *
 * @author tibo
 */
class Partition implements HasStatus
{
    public $filesystem = "";
    public $blocks = 0;
    public $used = 0;
    public $mounted = "";

    /**
     *
     * @var int time reference, in unix timestamp
     */
    public $time = 0;

    public function usedPercent()
    {
        return round(100.0 * $this->used / $this->blocks);
    }
    
    public function usedGB() : int
    {
        return (int) round($this->used / 1E6);
    }
    
    public function sizeGB() : int
    {
        return (int) round($this->blocks / 1E6);
    }
    
    public function status() : Status
    {
        if ($this->usedPercent() > 80) {
            return Status::warning();
        } elseif ($this->usedPercent() > 95) {
            return Status::error();
        }
        
        return Status::ok();
    }
}
