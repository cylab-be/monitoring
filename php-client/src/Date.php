<?php

namespace Monitor;

use Symfony\Component\Process\Process;

/**
 * Unix timestamp (can be used to check that time is correctly configured on
 * this server). date +%s
 *
 * @author tibo
 */
class Date implements SensorInterface {

    public function run() {
        $process = new Process('date +%s');
        $process->run();
        return $process->getOutput();

    }
}
