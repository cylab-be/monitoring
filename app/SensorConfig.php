<?php

namespace App;

/**
 * Description of SensorConfig
 *
 * @author tibo
 */
class SensorConfig
{
    public $label = "";
    public $trigger_label = "";
    
    public function __construct(string $label, string $trigger_label)
    {
        $this->label = $label;
        $this->trigger_label = $trigger_label;
    }
}
