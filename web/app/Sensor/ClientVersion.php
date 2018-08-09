<?php

namespace App\Sensor;

/**
 * Description of Reboot
 *
 * @author tibo
 */
class ClientVersion extends \App\AbstractSensor {

    public function latestVersion() {
        return json_decode(file_get_contents("https://gitlab.cylab.be/cylab/monitoring/raw/master/php-client/release/manifest.json"))[0]->version;
    }

    //put your code here
    public function report() {
        return "<p>Installed version: " . $this->getServer()->clientVersion() . "</p>"
        . "<p>Latest client version: " . $this->latestVersion() . "</p>";
    }

    public function status() {
        return self::STATUS_OK;
    }
}
