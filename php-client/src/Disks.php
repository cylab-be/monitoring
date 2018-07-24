<?php

namespace Monitor;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class Updates implements SensorInterface {

    public function run() {
        $process = new Process('cat /var/lib/update-notifier/updates-available');
        $process->run();
        return $process->getOutput();

    }
}
