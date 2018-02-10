<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organizations extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany('App\Models\User','users_organizations', 'organization_id','user_id')
            ->withTimestamps();
    }
    public function servers()
    {
        return $this->hasMany('App\Models\Server', 'organization_id');
    }
    
}
