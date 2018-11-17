<?php

namespace Monitor;

use Symfony\Component\Process\Process;

/**
 * dmidecode -t system
 *
 * @author tibo
 */
class System implements SensorInterface {

    public function run() {
        $process = new Process('dmidecode -t system');
        $process->run();
        return $process->getOutput();

    }
}
