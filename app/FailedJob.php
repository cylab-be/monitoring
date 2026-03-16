<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FailedJob extends Model
{
    protected $casts = ['payload' => 'object'];
}
