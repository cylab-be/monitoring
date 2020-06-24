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

    const MANIFEST = "https://download.cylab.be/monitor-php-client/manifest.json";

    public function manifest()
    {
        $options = [
            'timeout' => 5.0];

        $proxy = config("app.proxy", null);
        if ($proxy != null) {
            $options["proxy"] = $proxy;
        }

        $client = new Client($options);
        $json = $client->get(self::MANIFEST)->getBody();

        return json_decode($json)[0];
    }

    /**
     * Fetch the latest available version (e.g. "1.2.3")
     *
     * @throws \RuntimeException if a network problem occurs
     * @return string the latest available version (e.g. "1.2.3")
     */
    public function latestVersion() : string
    {
        return $this->manifest()->version;
    }

    public function latestUrl() : string
    {
        return $this->manifest()->url;
    }

    public function report(array $records) : string
    {
        return "<p>Installed version: " . $this->installedVersion($records) . "</p>"
        . "<p>Latest client version: " . $this->latestVersion() . "</p>";
    }

    public function installedVersion(array $records)
    {
        $last_record = end($records);
        if ($last_record == null) {
            return "none";
        }

        return $last_record->version;
    }

    public function status(array $records) : int
    {
        $latest_version = "unknown";

        try {
            $latest_version = $this->latestVersion();
        } catch (\ErrorException $ex) {
            return self::STATUS_UNKNOWN;
        }

        if ($this->installedVersion($records) === $latest_version) {
            return self::STATUS_OK;
        }

        return self::STATUS_WARNING;
    }
}
