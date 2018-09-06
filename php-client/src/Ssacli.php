<?php

namespace Monitor;

use Symfony\Component\Process\Process;

/**
 * Use SSACLI tool on HP servers to get state or RAID disks.
 *
 * @author tibo
 */
class Ssacli implements SensorInterface {

    public function run() {
        $process = new Process('ssacli ctrl all show config');
        $process->run();
        return $process->getOutput();

    }
}
