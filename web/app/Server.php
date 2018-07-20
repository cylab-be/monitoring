<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{

    protected $fillable = ["token"];

    public function __construct(array $attributes = array()) {
        $attributes["token"] = str_random(32);
        parent::__construct($attributes);
    }

    public function organization() {
        return $this->belongsTo("App\Organization");
    }
}
