<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{

    protected $fillable = ["token"];

    /**
     * Last record from this server (used for caching).
     * @var String
     */
    private $last_record = null;

    private static $sensors = [
        \App\Sensor\LoadAvg::class,
        \App\Sensor\MemInfo::class,
        \App\Sensor\Ifconfig::class,
        \App\Sensor\ListeningPorts::class,
        \App\Sensor\Reboot::class,
        \App\Sensor\Updates::class,
        \App\Sensor\Disks::class,
        \App\Sensor\Inodes::class,
        \App\Sensor\Ssacli::class,
        \App\Sensor\Perccli::class,
        \App\Sensor\Date::class,
        \App\Sensor\ClientVersion::class,
        \App\Sensor\Heartbeat::class,
        // \App\Sensor\DiskEvolution::class
    ];

    public function __construct(array $attributes = array())
    {
        $attributes["token"] = str_random(32);
        parent::__construct($attributes);
    }

    public function organization()
    {
        return $this->belongsTo("App\Organization");
    }

    public function lastRecord()
    {
        if ($this->last_record == null) {
            $collection = \Mongo::get()->monitoring->records;
            $this->last_record =  $collection->findOne(
                ["server_id" => $this->id],
                ["sort" => ["_id" => -1]]
            );
        }

        return $this->last_record;
    }

    /**
     * Return the last record if it contains the field $field,
     * otherwise return null.
     * @param string $field
     * @return string
     */
    public function lastRecordContaining(string $field)
    {
        if (isset($this->lastRecord()->$field)) {
            return $this->lastRecord();
        }

        return null;
    }

    /**
     *
     * @return \Carbon\Carbon
     */
    public function lastRecordTime()
    {
        $last_record = $this->lastRecord();
        if ($last_record === null) {
            return \Carbon\Carbon::createFromTimestamp(0);
        }

        return \Carbon\Carbon::createFromTimestamp($last_record->time);
    }

    public function clientVersion()
    {
        $last_record = $this->lastRecord();
        if ($last_record == null) {
            return "none";
        }

        return $last_record->version;
    }

    public function lastClientUrl()
    {
        $client_sensor = new \App\Sensor\ClientVersion($this);
        return $client_sensor->latestUrl();
    }

    /**
     * Get integer status of server.
     * @return int
     */
    public function status()
    {
        return max($this->statusArray());
    }

    public function statusBadge()
    {
        return AbstractSensor::getBadgeForStatus($this->status());
    }

    public function statusArray()
    {
        $status_array = [];
        foreach ($this->getSensors() as $sensor) {
            $status_array[\get_class($sensor)] = $sensor->status();
        }
        return $status_array;
    }

    public function getSensorsNOK()
    {
        $sensorsNOK = [];
        foreach ($this->getSensors() as $sensor) {
            if ($sensor->status() > 0) {
                $sensorsNOK[] = $sensor;
            }
        }
        return $sensorsNOK;
    }

    public function statusString()
    {
        return self::getNameForStatus($this->status());
    }

    public static function getNameForStatus($status)
    {
        switch ($status) {
            case 0:
                return "OK";
            case 10:
                return "WARNING";
            case 20:
                return "ERROR";
            default:
                return "Unknown";
        }
    }

    public function getBadge()
    {
        return AbstractSensor::getBadgeForStatus($this->status());
    }

    public function color()
    {
        return AbstractSensor::getColorForStatus($this->status());
    }

    public function getSensors()
    {

        $sensors = [];
        foreach (self::$sensors as $sensor) {
            $sensors[] = new $sensor($this);
        }
        return $sensors;
    }

    public function uptime()
    {
        $record = $this->lastRecordContaining("upaimte");
        if ($record == null) {
            return "";
        }

        return $this->parseUptime($record->upaimte);
    }

    public function parseUptime(string $string)
    {
        $pieces = explode(' ', $string);
        $uptime = \Carbon\Carbon::now()->subSeconds($pieces[0]);
        return $uptime->diffForHumans(null, true);
    }

    public function uuid()
    {
        $record = $this->lastRecordContaining("system");
        if ($record == null) {
            return "";
        }

        return $this->parseUUID($record->system);
    }

    const UUID = "/\s*UUID: (.*)/m";

    public function parseUUID(string $string)
    {
        $matches = array();
        preg_match(self::UUID, $string, $matches);
        return $matches[1];
    }


    public function cpuinfo()
    {
        $record = $this->lastRecordContaining("cpu");
        if ($record == null) {
            return null;
        }

        return $this->parseCpuinfo($record->cpu);
    }

    const CPU_INFO = "/^model name	: (.+)$/m";
    public function parseCpuinfo($string)
    {
        $matches = array();
        preg_match_all(self::CPU_INFO, $string, $matches);

        $result["threads"] = count($matches[0]);
        $result["cpu"] = $matches[1][0];
        return $result;
    }

    public function meminfo()
    {
        return round($this->memoryTotal() / 1000 / 1000) . " GB";
    }

    /**
     *
     * @return int total memory (in KB)
     */
    public function memoryTotal()
    {
        $record = $this->lastRecordContaining("memory");
        if ($record == null) {
            return null;
        }

        return $this->parseMeminfo($record->memory);
    }

    const MEMINFO = "/^MemTotal:\\s+([0-9]+) kB$/m";
    public function parseMeminfo($string)
    {
        $matches = array();
        preg_match(self::MEMINFO, $string, $matches);
        $total = $matches[1];
        return $total;
    }

    public function lsb()
    {

        $record = $this->lastRecordContaining("lsb");
        if ($record == null) {
            return "";
        }

        return $this->parseLsb($record->lsb);
    }

    const LSB = "/^Description:	(.+)$/m";
    public function parseLsb($string)
    {
        $matches = [];
        preg_match(self::LSB, $string, $matches);
        return $matches[1];
    }



    const REGEX_MANUFACTURER = "/^\s*Manufacturer: (.*)$/m";
    public function parseManufacturer($string)
    {
        $matches = [];
        preg_match(self::REGEX_MANUFACTURER, $string, $matches);
        return $matches[1];
    }

    public function manufacturer()
    {
        $record = $this->lastRecordContaining("system");
        if ($record == null) {
            return "Unknown";
        }

        return $this->parseManufacturer($record->system);
    }

    const REGEX_PRODUCT_NAME = "/^\s*Product Name: (.*)$/m";
    public function parseProductName($string)
    {
        $matches = [];
        preg_match(self::REGEX_PRODUCT_NAME, $string, $matches);
        return $matches[1];
    }

    public function productName()
    {
        $record = $this->lastRecordContaining("system");
        if ($record == null) {
            return "";
        }

        return $this->parseProductName($record->system);
    }

    public function getChanges($count = 10)
    {
        return \App\StatusChange::getLastChangesForServer($this->id, $count);
    }

    public static function id($id) : Server
    {
        return self::where("id", $id)->first();
    }
}
