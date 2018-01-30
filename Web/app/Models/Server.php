<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = ['name'];

    public function organization()
    {
        return $this->belongsTo('App\Models\Organizations');
    }
}
