<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

/**
 * Description of AbstractSensor
 *
 * @author tibo
 */
abstract class AbstractSensor implements Sensor {
    /**
     *
     * @var \App\Server
     */
    private $server;

    public function __construct(\App\Server $server) {
        $this->server = $server;
    }

    protected function getServer() {
        return $this->server;
    }

    function getLastRecord($field) {
        return \Mongo::get()->monitoring->records->findOne(
                [   "server_id" => $this->server->id,
                    $field => ['$ne' => null]],
                ["sort" => ["_id" => -1]]);
    }
}
