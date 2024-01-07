<?php

namespace App\Sensor;

use App\Status;
use App\HasStatus;

/**
 * Description of InodesDisk
 *
 * @author tibo
 */
class InodesDisk implements HasStatus
{
    public $filesystem = "";
    public $inodes = 0;
    public $used = 0;
    public $mounted = "";

    public function usedPercent()
    {
        return round(100.0 * $this->used / $this->inodes);
    }
    
    public function status() : Status
    {
        if ($this->usedPercent() > 95) {
            return Status::error();
        }
        
        if ($this->usedPercent() > 80) {
            return Status::warning();
        }
        
        return Status::ok();
    }
}
