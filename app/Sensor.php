<?php

namespace App;

/**
 * Base (abstract) class for sensors.
 *
 * @author tibo
 */
abstract class Sensor
{

    private $server;

    public function __construct(?Server $server = null)
    {
        $this->server = $server;
    }

    protected function server() : Server
    {
        return $this->server;
    }

    /**
     * Get the name of the sensor. Can be overridden by sub-classes to provide
     * a more meaningful name.
     *
     * @return string
     */
    public function name() : string
    {
        return (new \ReflectionClass($this))->getShortName();
    }
    
    /**
     * Compute the status code from an array of Record.
     */
    abstract public function status(array $records) : int;
    
    /**
     * Create the HTML report describing the result of this sensor's analysis.
     */
    abstract public function report(array $records) : string;
}
