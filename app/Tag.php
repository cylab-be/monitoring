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
}
