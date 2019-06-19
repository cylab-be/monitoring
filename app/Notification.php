<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $dateFormat = 'U';


    public function server()
    {
        return $this->belongsTo('\App\Server');
    }


}
