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
        if (! Auth::user()->organizations->contains($org->id)) {
            Auth::user()->organizations()->attach($org->id);
        }
        return view("org/manage",['organizations' => Auth::user()->organizations()->get()]);
    }
    public function details($name){
        $org =  Organizations::where('name',$name)->first();
        if($org==null)abort(404);
        $servers = $org->servers()->get();
        foreach($servers as $server){
            $server->sensors();
        }
       return view("org/detail",['organization' => $org , 'servers' => $servers]);
    }
    public function addServer(Request $request){
        $server =  Server::where('token',$request->input('token'))->first();
        if($server !=null){
            $org =  Organizations::where('name',$request->input('organization'))->first();
            if (Auth::user()->organizations->contains($org->id)) {
                $org->servers()->save($server);
                return "ok";
            }
           return redirect()->back();
        }

        return redirect()->back();
    }
}
