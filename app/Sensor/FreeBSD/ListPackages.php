<?php

namespace App\Sensor\FreeBSD;

use App\Record;
use App\Sensor;
use App\SensorConfig;
use App\Report;
use App\Status;

/**
 * Description of ListPackages
 *
 * @author tibo
 */
class ListPackages extends Sensor
{
    #[\Override]
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "freebsd-list-packages",
            "freebsd-pkg-info",
            "Run and parse pkg info to list installed packages",
            ["freebsd-pkg-info" => "command -v pkg >/dev/null 2>&1 && pkg info"]
        );
    }


    #[\Override]
    public function analyze(Record $record): ?Report
    {
        return (new Report())
            ->setTitle("FreeBSD Packages")
                ->setStatus(Status::ok())
                ->setHTML("<pre>" . $record->data . "</pre>");
    }
    
    /**
     * Return an array of packages
     * @param string $data
     * @return array
     */
    public static function parse(string $data) : array
    {
        return explode("\n", $data);
    }
}
