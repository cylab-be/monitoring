<?php

namespace App\Sensor;

/**
 * Represents a single memory slot
 *
 * @author tibo
 */
class MemoryDevice
{
    /**
     *
     * @var int size in GB
     */
    public int $size = 0;
    public $locator = "";
    public $type = "";
    public $speed = "";
    public $manufacturer = "";
    public $part_number = "";
    public $configured_speed = "";
}
