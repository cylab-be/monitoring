<?php

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
        return $this->server->lastRecordContaining($field);
    }

    /**
     * Get the last $count records containing $field.
     * !! $count is the MAXIMUM number of returned records.
     * To optimize mongo's usage of index, we get the last $count records
     * then filter locally for records containing this record
     * @param type $field
     * @param type $count
     * @return type
     */
    function getLastRecords($field, $count) {
        $records = \Mongo::get()->monitoring->records->find(
                ["server_id" => $this->server->id],
                ["limit" => $count, "sort" => ["_id" => -1]]);

        $results = [];
        foreach ($records as $record) {
            if (isset($record->$field)) {
                $results[] = $record;
            }
        }

        return $results;
    }
}
