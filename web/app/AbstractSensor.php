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

    public function getName() : string {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * Return the last record if it contains the field $field,
     * otherwise return null.
     * @param string $field
     * @return
     */
    function getLastRecord (string $field) {
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

    public static function getColorForStatus($status) {
        switch ($status) {
            case 0:
                return 'success';
            case 10:
                return 'warning';
            case 20:
                return 'danger';
            default:
                return 'secondary';
        }
    }

    public static function getBadgeForStatus($status) {
        switch ($status) {
            case 0:
                return '<span class="badge badge-success">OK</span>';
            case 10:
                return '<span class="badge badge-warning">WARNING</span>';
            case 20:
                return '<span class="badge badge-danger">ERROR</span>';
            default:
                return '<span class="badge badge-secondary">Unknown</span>';
        }
    }

    public function getBadge() {
        return self::getBadgeForStatus($this->status());
    }
}
