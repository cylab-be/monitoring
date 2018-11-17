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

    static $sensors = [
        \App\Sensor\LoadAvg::class,
        \App\Sensor\MemInfo::class,
        \App\Sensor\Reboot::class,
        \App\Sensor\Updates::class,
        \App\Sensor\Disks::class,
        \App\Sensor\Inodes::class,
        \App\Sensor\Ssacli::class,
        \App\Sensor\Date::class,
        \App\Sensor\ClientVersion::class
    ];

    public function __construct(array $attributes = array()) {
        $attributes["token"] = str_random(32);
        parent::__construct($attributes);
    }

    public function organization() {
        return $this->belongsTo("App\Organization");
    }

    public function lastRecord() {
        if ($this->last_record == null) {

            $collection = \Mongo::get()->monitoring->records;
            $this->last_record =  $collection->findOne(
                    ["server_id" => $this->id],
                    ["sort" => ["_id" => -1]]);
        }

        return $this->last_record;
    }

    public function lastRecordContaining($field) {
        if (isset($this->lastRecord()->$field)) {
            return $this->lastRecord();
        }

        return null;
    }

    /**
     *
     * @return \DateTimeZone
     */
    public function lastRecordTime() {
        $last_record = $this->lastRecord();
        if ($last_record === null) {
            return \Carbon\Carbon::createFromTimestamp(0);
        }

        return \Carbon\Carbon::createFromTimestamp($last_record->time);
    }

    public function clientVersion() {
        $last_record = $this->lastRecord();
        if ($last_record == null) {
            return "none";
        }

        return $last_record->version;
    }

    /**
     * Get integer status of server.
     * @return int
     */
    public function status() {
        $all_status = [];
        foreach ($this->getSensors() as $sensor) {
            $all_status[] = $sensor->status();
        }

        return max($all_status);
    }

    public function statusString() {
        switch ($this->status()) {
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

    public function getSensors() {

        $sensors = [];
        foreach (self::$sensors as $sensor) {
            $sensors[] = new $sensor($this);
        }
        return $sensors;
    }



    public function cpuinfo() {
        $record = $this->lastRecordContaining("cpu");
        if ($record == null) {
            return null;
        }

        return $this->parseCpuinfo($record->cpu);
    }

    const CPU_INFO = "/^model name	: (.+)$/m";
    public function parseCpuinfo($string) {
        $matches = array();
        preg_match_all(self::CPU_INFO, $string, $matches);

        $result["threads"] = count($matches[0]);
        $result["cpu"] = $matches[1][1];
        return $result;
    }

    public function meminfo() {
        return round($this->memoryTotal() / 1000 / 1000) . " GB";
    }

    /**
     *
     * @return int total memory (in KB)
     */
    public function memoryTotal() {
        $record = $this->lastRecordContaining("memory");
        if ($record == null) {
            return null;
        }

        return $this->parseMeminfo($record->memory);
    }

    const MEMINFO = "/^MemTotal:\\s+([0-9]+) kB$/m";
    public function parseMeminfo($string) {
        $matches = array();
        preg_match(self::MEMINFO, $string, $matches);
        $total = $matches[1];
        return $total;
    }

    public function lsb() {

        $record = $this->lastRecordContaining("lsb");
        if ($record == null) {
            return "";
        }

        return $this->parseLsb($record->lsb);
    }

    const LSB = "/^Description:	(.+)$/m";
    public function parseLsb($string) {
        $matches = [];
        preg_match(self::LSB, $string, $matches);
        return $matches[1];
    }
}
