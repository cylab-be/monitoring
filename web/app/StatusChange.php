<?php

namespace App;

/**
 * Represents a change of status, that will be saved in MongoDB.
 *
 * @author tibo
 */
class StatusChange {

    public $server_id = 0;
    public $status = 0;
    public $time = 0;

    public static function save($status) {
        $data = [
            "time" => time(),
            "server_id" => $status->server_id,
            "status" => $status->status,

        ];

        $collection = \Mongo::get()->monitoring->statuschanges;
        $collection->insertOne($data);
    }

    public static function getLastChangeForServer(int $server_id) : StatusChange {
        $collection = \Mongo::get()->monitoring->statuschanges;
        $record = $collection->findOne(
                    ["server_id" => $server_id],
                    ["sort" => ["_id" => -1]]);

        $change = new StatusChange();
        $change->server_id = $server_id;

        if ($record == null) {
            return $change;
        }

        $change->time = $record["time"];
        $change->status = $record["status"];
        return $change;
    }
}
