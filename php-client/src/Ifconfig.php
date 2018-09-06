<?php

namespace Monitor;

use Symfony\Component\Process\Process;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class Ifconfig implements SensorInterface {

    public function run() {
        $process = new Process('ifconfig');
        $process->run();
        return $process->getOutput();

    }
}
