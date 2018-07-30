<?php

namespace Monitor;

use Symfony\Component\Process\Process;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class CPUInfo implements SensorInterface {

    public function run() {
        $process = new Process('cat /proc/cpuinfo');
        $process->run();
        return $process->getOutput();

    }
}
