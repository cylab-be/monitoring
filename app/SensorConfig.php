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

    /**
     *
     * @param string $label label for the generated report (if any)
     * @param string $trigger_label
     * @param string|null $description
     */
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
