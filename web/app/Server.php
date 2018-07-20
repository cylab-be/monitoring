<?php

namespace App;

use MongoDB\Client as Mongo;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{

    protected $fillable = ["token"];

    public function __construct(array $attributes = array()) {
        $attributes["token"] = str_random(32);
        parent::__construct($attributes);
    }

    public function organization() {
        return $this->belongsTo("App\Organization");
    }

    public function lastRecord() {
        $collection = (new Mongo)->monitoring->records;
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
}
