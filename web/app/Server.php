<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{

    protected $fillable = ["token"];

    static $sensors = [
        \App\Sensor\LoadAvg::class,
        \App\Sensor\Reboot::class,
        \App\Sensor\Update::class
    ];

    public function __construct(array $attributes = array()) {
        $attributes["token"] = str_random(32);
        parent::__construct($attributes);
    }

    public function organization() {
        return $this->belongsTo("App\Organization");
    }

    public function lastRecord() {
        $collection = \Mongo::get()->monitoring->records;
        return $collection->findOne(
                ["server_id" => $this->id],
                ["sort" => ["_id" => -1]]);
    }

    /**
     *
     * @return \DateTimeZone
     */
    public function lastRecordTime() {
        return \Carbon\Carbon::createFromTimestamp($this->lastRecord()->time);
    }

    public function clientVersion() {
        return $this->lastRecord()->version;
    }

    public function status() {
        return "OK";
    }

    public function getSensors() {

        $sensors = [];
        foreach (self::$sensors as $sensor) {
            $sensors[] = new $sensor($this);
        }
        return $sensors;
    }
}
