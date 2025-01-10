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
    public $description = "";
    
    public function __construct(
        string $label,
        string $trigger_label,
        ?string $description = null
    ) {
        $this->label = $label;
        $this->trigger_label = $trigger_label;
        
        $this->description = $description ?? "";
    }
}
