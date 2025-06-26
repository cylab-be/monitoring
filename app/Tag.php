<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property Organization $organization
 */
class Tag extends Model
{
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function servers()
    {
        return $this->belongsToMany(Server::class);
    }
}
