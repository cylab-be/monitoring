<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizations;
use App\Models\Server;
use App\Models\Sensors;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    public function index(){
        return view("org/manage",['organizations' => Auth::user()->organizations()->get()]);
    }
    public function addOrg(Request $request){
        $org =  Organizations::where('name',$request->input('name'))->first();
        Auth::user()->organizations()->attach($org->id);
        return view("org/manage",['organizations' => Auth::user()->organizations()->get()]);
    }
    public function details($name){
        $org =  Organizations::where('name',$name)->first();
        if($org==null)abort(404);
        $servers = $org->servers()->get();
        foreach($servers as $server){
            $server->sensors($server->id);
        }
       return view("org/detail",['organization' => $org , 'servers' => $servers]);
    }
}
