<?php

namespace App\Http\Controllers;

use App\Models\Sensors;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $model;
    public function __construct(Sensors $sensor)
    {
        $this->middleware('auth');
        $this->model = $sensor;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("home",['sensor' => Sensors::all()]);
    }
}
