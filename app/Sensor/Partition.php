<?php

namespace App\Sensor;

/**
 * Description of Partition
 *
 * @author tibo
 */
class Partition
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
        return round($this->used / 1E6);
    }
    
    public function sizeGB() : int
    {
        return round($this->blocks / 1E6);
    }
}
