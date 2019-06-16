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

    public function usedPercent()
    {
        return round(100.0 * $this->used / $this->blocks);
    }
}
