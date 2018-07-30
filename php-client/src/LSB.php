<?php

namespace Monitor;

use Symfony\Component\Process\Process;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class LSB implements SensorInterface {

    public function run() {
        $process = new Process('lsb_release -a');
        $process->run();
        return $process->getOutput();

    }
}
