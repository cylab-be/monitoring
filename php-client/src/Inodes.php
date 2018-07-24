<?php

namespace Monitor;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class Inodes implements SensorInterface {

    public function run() {
        $process = new Process('df -i');
        $process->run();
        return $process->getOutput();

    }
}
