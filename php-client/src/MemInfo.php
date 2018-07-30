<?php

namespace Monitor;

use Symfony\Component\Process\Process;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class MemInfo implements SensorInterface {

    public function run() {
        $process = new Process('cat /proc/meminfo');
        $process->run();
        return $process->getOutput();

    }
}
