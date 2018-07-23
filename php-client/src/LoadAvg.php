<?php

namespace Monitor;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class LoadAvg implements SensorInterface {

    public function run() {
        $process = new Process('cat /proc/loadavg');
        $process->run();
        return $process->getOutput();

    }
}
