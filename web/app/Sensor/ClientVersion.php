<?php

namespace App\Sensor;

/**
 * Description of Reboot
 *
 * @author tibo
 */
class ClientVersion extends \App\AbstractSensor {

    const MANIFEST = "https://gitlab.cylab.be/cylab/monitoring/raw/master/php-client/release/manifest.json";

    public function latestVersion() {
        $ctx = stream_context_create(array('http' => ['timeout' => 5]));
        $json = @ \file_get_contents(self::MANIFEST, false, $ctx);
        if ($json === FALSE) {
            return "";
        }

        return json_decode($json)[0]->version;
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
