<?php

namespace App\Sensor;

use GuzzleHttp\Client;

/**
 * Description of Reboot
 *
 * @author tibo
 */
class ClientVersion extends \App\AbstractSensor
{

    const MANIFEST = "https://gitlab.cylab.be/cylab/monitoring/raw/master/php-client/release/manifest.json";

    public function latestVersion()
    {
        $client = new Client([
            'timeout'  => 5.0,
        ]);

        try {
            $json = $client->get(self::MANIFEST)->getBody();
        } catch (\Exception $ex) {
            return "";
        }

        return json_decode($json)[0]->version;
    }

    //put your code here
    public function report()
    {
        return "<p>Installed version: " . $this->getServer()->clientVersion() . "</p>"
        . "<p>Latest client version: " . $this->latestVersion() . "</p>";
    }

    public function status()
    {
        if ($this->getServer()->clientVersion() === $this->latestVersion()) {
            return self::STATUS_OK;
        }

        return self::STATUS_WARNING;
    }
}
