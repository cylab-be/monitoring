<?php

namespace App;

use Illuminate\Support\Facades\Log;

class SensorWrapper implements Sensor
{
    private $sensor;

    private $report;
    private $status;

    public function __construct(Sensor $sensor) {
        $this->sensor = $sensor;
    }

    public function id() : string
    {
        return \get_class($this->sensor);
    }

    public function name(): string
    {
        return $this->sensor->name();
    }

    public function report(array $records): string {
        if (is_null($this->report)) {
            try {
                $this->report = $this->sensor->report($records);
            } catch (\Exception $ex) {
                Log::error('Sensor failed : ' . $ex->getTraceAsString());
                $this->report = "<p>Sensor " . $this->getName() . " failed :-(</p>";
            }
        }

        return $this->report;
    }

    public function status(array $records): int {
        if (is_null($this->status)) {
            try {
                $this->status = $this->sensor->status($records);
            } catch (\Exception $ex) {
                Log::error('Sensor failed : ' . $ex->getTraceAsString());
                $this->status = self::STATUS_UNKNOWN;
            }
        }

        return $this->status;
    }

    public function getBadge(array $records) : string
    {
        return self::getBadgeForStatus($this->status($records));
    }

    public static function getBadgeForStatus(int $status) : string
    {
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

}