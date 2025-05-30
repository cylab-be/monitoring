<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $ip
 * @property string $comment
 * @property Server $server
 */
class Ip extends Model
{
    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
