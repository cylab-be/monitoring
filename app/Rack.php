<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    public function servers()
    {
        return $this->hasMany(Server::class);
    }
}
