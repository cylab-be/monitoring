<?php

namespace App\Sensor;

/**
 * Represents a single listening port
 *
 * @author tibo
 */
class ListeningPort
{
    public $port;
    public $proto;
    public $bind;
    public $process;
    
    public function __toString()
    {
        $this->port . " | " .
                $this->proto . " | " .
                $this->bind . " | " .
                $this->process;
    }
}
