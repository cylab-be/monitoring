<?php

namespace App\Http\Controllers;

use App\Models\Organizations;
use App\Models\Server;
use App\Models\Sensors;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organization =  Auth::user()->organizations()->get();
        foreach($organization as $org){
            $servers = $org->servers()->get();
            foreach($servers as $server){
                $server->lastState = $server->getLastState();
            }
            $org->servers = $servers;
        }
        return view("home",['organization' => $organization]);
    }
}
