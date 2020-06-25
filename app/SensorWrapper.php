<?php

namespace App;

use Illuminate\Support\Facades\Log;

class SensorWrapper
{
    private $sensor;

    private $report;
    private $status;

    public function __construct(Sensor $sensor)
    {
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

    public function report(array $records): string
    {
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

    public function status(array $records): Status
    {
        if (is_null($this->status)) {
            try {
                $this->status = new Status($this->sensor->status($records));
            } catch (\Exception $ex) {
                Log::error('Sensor failed : ' . $ex->getTraceAsString());
                $this->status = new Status(Status::UNKNOWN);
            }
        }

        return $this->status;
    }
}
