<?php

namespace App\Sensor;

use App\Status;
use App\HasStatus;

/**
 * Description of Temperature (core)
 *
 * @author helha
 */

class Core implements HasStatus
{
    public $name = "";      // eg : core 0
    public $value;          // eg : 42.5
    public $critvalue;      // eg : 76.0
    
    public function __construct(string $name, float $value, float $critvalue)
    {
        $this->name = $name;
        $this->value = $value;
        $this->critvalue = $critvalue;
    }
    
    public function status() : Status
    {
        if ($this->value >= $this->critvalue) {
            return Status::warning();
        }
        
        return Status::ok();
    }
}
