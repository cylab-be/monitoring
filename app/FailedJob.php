<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FailedJob extends Model
{
    protected $casts = ['payload' => 'object'];
    
    public static function countInLastHour() : int
    {
        return self::where('failed_at', '>', Carbon::now()->subHour())->count();
    }
}
