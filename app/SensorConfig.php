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
    public $commands = [];

    /**
     *
     * @param string $label label for the generated report (if any)
     * @param string $trigger_label
     * @param string|null $description
     * @param array|null $commands commands to execute on the client device
     */
    public function __construct(
        string $label,
        string $trigger_label,
        ?string $description = null,
        ?array $commands = []
    ) {
        $this->label = $label;
        $this->trigger_label = $trigger_label;

        $this->description = $description ?? "";
        $this->commands = $commands ?? [];
    }
}
