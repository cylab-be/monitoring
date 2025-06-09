<?php

namespace App\Sensor;

/**
 * A dataset that can be fed to Chart.js
 *
 * @author tibo
 */
class Dataset
{
    public $label;
    public $data = [];

    // Default transparent
    public $backgroundColor = "rgba(255, 255, 255, 0.0)";

    // Default grey
    public $borderColor;

    public function __construct(string $label, ?string $borderColor = "rgba(0, 0, 0, .125)")
    {
        $this->label = $label;
        $this->borderColor = $borderColor;
    }

    public function add(Point $point)
    {
        $this->data[] = $point;
    }
}
