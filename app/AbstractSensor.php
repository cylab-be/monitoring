<?php

namespace App;

use Illuminate\Support\Facades\Log;

/**
 * Description of AbstractSensor
 *
 * @author tibo
 */
abstract class AbstractSensor implements Sensor
{
    /**
     *
     * @var \App\Server
     */
    private $server;

    public function __construct(\App\Server $server)
    {
        $this->server = $server;
    }

    protected function getServer() : \App\Server
    {
        return $this->server;
    }

    public function getName() : string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public static function getColorForStatus(int $status) : string
    {
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

    public function getBadge() : string
    {
        return self::getBadgeForStatus($this->status());
    }

    public function reportHTML() : string
    {
        try {
            return $this->report();
        } catch (\Exception $ex) {
            Log::error('Sensor failed : ' . $ex->getTraceAsString());
            return "<p>Sensor " . $this->getName() . " failed :-(</p>";
        }
    }
}
