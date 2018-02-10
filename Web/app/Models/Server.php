<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = ['name'];
    public $sensors;
    protected $primaryKey = 'id';
    public $lastState;
    public function organization()
    {
        return $this->belongsTo('App\Models\Organizations');
    }

    public function sensors()
    {
        $sensors = Sensors::where("server_id","".$this->getKey())->get();
        foreach($sensors as $sensor){
            $sensor["content"] = json_decode($sensor["content"]);
        }
        return $this->sensors = $sensors;
    }
    public function getLastState()
    {
        $sensor = Sensors::where("server_id","".$this->getKey())->orderBy('created_at', 'desc')->first();
        $sensor["content"] = json_decode($sensor["content"]);

        return $this->lastState = $sensor;
    }
}
