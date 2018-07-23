<?php

namespace Monitor;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class Reboot implements SensorInterface {

    public function run() {
        return file_exists("/var/run/reboot-required");

    }
}
