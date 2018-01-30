<?php

namespace App\Models;

use Moloquent;

class Sensors extends Moloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'sensors';
    protected $fillable = ['id','content'];

}