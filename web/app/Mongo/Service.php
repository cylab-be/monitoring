<?php

namespace App\Mongo;

use MongoDB\Client;

class Service
{

    private $mongo;

    public function __construct($uri = null, $uriOptions = [], $driverOptions = [])
    {
        $this->mongo = new Client($uri, $uriOptions, $driverOptions);
    }

    public function get()
    {
        return $this->mongo;
    }
}
