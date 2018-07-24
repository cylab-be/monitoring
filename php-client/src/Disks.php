<?php

namespace Monitor;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class Disks implements SensorInterface {

    public function run() {
        $process = new Process('df');
        $process->run();
        return $process->getOutput();

    }
}
