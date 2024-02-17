<?php

namespace App\Sensor;

/**
 * Description of Memory
 *
 * @author tibo
 */
class Memory
{
    public $total;
    public $free;
    public $cached;

    public function __construct(int $total, int $free, int $cached)
    {
        $this->total = $total;
        $this->free = $free;
        $this->cached = $cached;
    }

    public function used() : int
    {
        return $this->total - $this->free - $this->cached;
    }


    public function usedRatio() : float
    {
        return $this->used() / $this->total;
    }
}
