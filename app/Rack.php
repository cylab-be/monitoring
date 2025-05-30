<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Organization $organization
 */
class Rack extends Model
{
    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
