<?php

namespace App;

use \Carbon\Carbon;

/**
 * Represents a change of status, that will be saved in MongoDB.
 *
 * @author tibo
 */
class StatusChange
{

    public $server_id = 0;
    public $status = 0;
    public $time = 0;

    public function parse($array)
    {
        if ($array == null) {
            return;
        }

        $fields = ["server_id", "status", "time"];
        foreach ($fields as $field) {
            if (isset($array[$field])) {
                $this->$field = $array[$field];
            }
        }

        return $this;
    }

    public function getStatusBadge()
    {
        return AbstractSensor::getBadgeForStatus($this->status);
    }

    public function getTimeCarbon() : Carbon
    {
        return Carbon::createFromTimestamp($this->time);
    }

    public function server() : Server
    {
        return Server::id($this->server_id);
    }

    public static function save($status)
    {
        $data = [
            "time" => time(),
            "server_id" => $status->server_id,
            "status" => $status->status,

        ];

        $collection = \Mongo::get()->monitoring->statuschanges;
        $collection->insertOne($data);
    }

    public static function getLastChangesForServer(int $server_id, int $count) : array
    {
        $collection = \Mongo::get()->monitoring->statuschanges;
        $records = $collection->find(
            ["server_id" => $server_id],
            ["limit" => $count, "sort" => ["_id" => -1]]
        );

        $changes = [];
        foreach ($records as $record) {
            $changes[] = (new StatusChange())->parse($record);
        }
        return $changes;
    }

    public static function getLastChangeForServer(int $server_id) : StatusChange
    {
        $collection = \Mongo::get()->monitoring->statuschanges;
        $record = $collection->findOne(
            ["server_id" => $server_id],
            ["sort" => ["_id" => -1]]
        );

        $change = new StatusChange();
        $change->server_id = $server_id;
        $change->parse($record);
        return $change;
    }
}
