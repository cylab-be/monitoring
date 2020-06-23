<?php

namespace App;

interface Sensor
{
    const STATUS_UNKNOWN = -1;
    const STATUS_OK = 0;
    const STATUS_WARNING = 10;
    const STATUS_ERROR = 20;

    public function __construct(Server $server);
    public function status(array $records) : int;
    public function report(array $records) : string;

    /**
     * Get the name of this sensor (e.g meminfo, cpuload, heartbeat)
     * @return string
     */
    public function getName() : string;
}
