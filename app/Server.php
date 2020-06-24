<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Server extends Model
{

    protected $fillable = ["token"];

    /**
     * Last record from this server (used for caching).
     * @var String
     */
    private $last_record = null;

    private $records_1day = null;

    private static $sensors = [
        \App\Sensor\LoadAvg::class,
        \App\Sensor\MemInfo::class,
        \App\Sensor\Ifconfig::class,
        \App\Sensor\Netstat::class,
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
        \App\Sensor\DiskEvolution::class,
        \App\Sensor\CPUtemperature::class,
        \App\Sensor\USBtemperature::class
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
     * Get the last day of data.
     * @return type
     */
    public function lastRecords1Day()
    {
        if ($this->records_1day !== null) {
            return $this->records_1day;
        }

        $start = time() - 24 * 3600;

        $records = \Mongo::get()->monitoring->records->find([
                "server_id" => $this->id,
                "time" => ['$gte' => $start]])
                ->toArray();
        return $records;
    }

    public function hasData() : bool
    {
        return $this->lastRecord() != null;
    }

    /**
     *
     * @return \Carbon\Carbon
     */
    public function lastRecordTime()
    {
        $hearbeat = new \App\Sensor\Heartbeat($this);
        return $hearbeat->lastRecordTime($this->lastRecord());
    }

    public function clientVersion(array $records) : string
    {
        $sensor = new \App\Sensor\ClientVersion($this);
        return $sensor->installedVersion($records);
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
    public function status(array $records)
    {
        return max($this->statusArray($records));
    }

    public function statusBadge(array $records)
    {
        return AbstractSensor::getBadgeForStatus($this->status($records));
    }

    public function statusArray(array $records)
    {
        $status_array = [];
        foreach ($this->getSensors() as $sensor) {
            $sensor_name = \get_class($sensor);
            try {
                $status_array[$sensor_name] = $sensor->status($records);
            } catch (\Exception $ex) {
                $status_array[$sensor_name] = Sensor::STATUS_UNKNOWN;
                Log::error("Sensor $sensor_name failed : " . $ex->getTraceAsString());
            }
        }
        return $status_array;
    }

    public function getSensorsNOK(array $records)
    {
        $sensorsNOK = [];
        foreach ($this->getSensors() as $sensor) {
            if ($sensor->status($records) > 0) {
                $sensorsNOK[] = $sensor;
            }
        }
        return $sensorsNOK;
    }

    public static function getNameForStatus(int $status)
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

    public function getBadge(array $records)
    {
        return AbstractSensor::getBadgeForStatus($this->status($records));
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

    /**
     * Human readable uptime.
     *
     * @return string
     */
    public function uptime() : string
    {
        $record = $this->lastRecord();
        if (! isset($record["upaimte"])) {
            return "unknown";
        }

        return $this->parseUptime($record->upaimte);
    }

    public function parseUptime(string $string) : string
    {
        $pieces = explode(' ', $string);
        $uptime = \Carbon\Carbon::now()->subSeconds($pieces[0]);
        return $uptime->diffForHumans(null, true);
    }

    public function uuid()
    {
        $record = $this->lastRecord();
        if (! isset($record["system"])) {
            return "";
        }

        return $this->parseUUID($record->system);
    }

    const UUID = "/\s*UUID: (.*)/m";

    public function parseUUID(string $string) : string
    {
        $matches = array();
        preg_match(self::UUID, $string, $matches);
        if (! isset($matches[1])) {
            return "unknown";
        }
        return $matches[1];
    }


    public function cpuinfo() : array
    {
        $record = $this->lastRecord();
        if (! isset($record["cpu"])) {
            return ["threads" => 0,
                "cpu" => "unknown"];
        }

        return $this->parseCpuinfo($record->cpu);
    }

    const CPU_INFO = "/^model name	: (.+)$/m";
    public function parseCpuinfo($string) : array
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
        $record = $this->lastRecord();
        if (! isset($record["memory"])) {
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

        $record = $this->lastRecord();
        if (! isset($record["lsb"])) {
            return "unknown";
        }

        return $this->parseLsb($record->lsb);
    }

    const LSB = "/^Description:	(.+)$/m";
    public function parseLsb($string) : string
    {
        $matches = [];
        preg_match(self::LSB, $string, $matches);
        return $matches[1];
    }


    const REGEX_MANUFACTURER = "/^\s*Manufacturer: (.*)$/m";
    public function parseManufacturer(string $string) : string
    {
        $matches = [];
        preg_match(self::REGEX_MANUFACTURER, $string, $matches);

        if (!isset($matches[1])) {
            return "unkwnown";
        }
        return $matches[1];
    }

    public function manufacturer()
    {
        $record = $this->lastRecord();
        if (! isset($record["system"])) {
            return "unknown";
        }

        return $this->parseManufacturer($record->system);
    }

    const REGEX_PRODUCT_NAME = "/^\s*Product Name: (.*)$/m";
    public function parseProductName(string $string) : string
    {
        $matches = [];
        preg_match(self::REGEX_PRODUCT_NAME, $string, $matches);
        if (!isset($matches[1])) {
            return "unkwnown";
        }
        return $matches[1];
    }

    public function productName()
    {
        $record = $this->lastRecord();
        if (! isset($record["system"])) {
            return "unknown";
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
