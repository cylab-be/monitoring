<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $time
 * @property int $server_id
 * @property Server $server
 * @property string $data
 */
class Record extends Model
{
    public $timestamps = false;
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];
    
    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
