<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property-read int $id
 * @property int $time
 * @property int $server_id
 * @property Server $server
 * @property string $data
 * @property string $label
 */
class Record extends Model
{
    public $timestamps = false;

    public function getDataAttribute(string $value): string
    {
        return gzinflate(base64_decode($value));
    }

    public function setDataAttribute(string $value)
    {
        $this->attributes['data'] = base64_encode(gzdeflate($value));
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function save(array $options = [])
    {
        if (parent::save($options)) {
            // If this record is saved inside a DB transaction, make sure
            // the agent is dispatched only after commit.
            $record = $this;
            if (DB::connection()->transactionLevel() > 0) {
                DB::afterCommit(function () use ($record) {
                    AgentScheduler::get()->notify($record);
                });
            } else {
                AgentScheduler::get()->notify($record);
            }
            return true;
        }

        return false;
    }

    public function time() : Carbon
    {
        return Carbon::createFromTimestamp($this->time);
    }
}
