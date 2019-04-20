<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

    public function __construct($total, $free, $cached)
    {
        $this->total = $total;
        $this->free = $free;
        $this->cached = $cached;
    }

    public function used()
    {
        return $this->total - $this->free - $this->cached;
    }
}
