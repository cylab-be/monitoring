<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;

/**
 * Represents a change of status.
 *
 * @property int $time
 * @property int $server_id
 * @property int $status
 * @property int $record_id
 *
 * @author tibo
 */
class StatusChange extends Model
{
    public $timestamps = false;

    public function status() : Status
    {
        return new Status($this->status);
    }

    public function getTimeCarbon() : Carbon
    {
        return Carbon::createFromTimestamp($this->time);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
