<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
    
    public function server()
    {
        return $this->belongsTo(Server::class);
    }
    
    public function save(array $options = []) {
        if (parent::save($options)) {
        
            AgentScheduler::get()->notify($this);
            return true;
        }
        
        return false;
    }
}
