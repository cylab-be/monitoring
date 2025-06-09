<?php

namespace App\Sensor;

/**
 * Description of Dataset
 *
 * @author tibo
 */
class Dataset
{
    public $label;
    public $data = [];
    public $backgroundColor = "rgba(255, 255, 255, 0.0)";
    public $borderColor = "#007bff";

    public function __construct(string $label, ?string $borderColor)
    {
        $this->label = $label;

        if (!is_null($borderColor)) {
            $this->borderColor = $borderColor;
        }
    }

    public function add(Point $point)
    {
        $this->data[] = $point;
    }
}
