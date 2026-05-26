<?php

namespace App\Http\Controllers;

use App\Organization;
use App\Key;

use Illuminate\Http\Request;

class OrganizationKeysController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /** Show form **/
    public function create(Organization $organization)
    {
        $this->authorize("update", $organization);
        return view("organization.key.create", ["organization" => $organization]);
    }
    
    public function store(Organization $organization, Request $request)
    {
        $this->authorize("update", $organization);
        $request->validate(["name" => "required|string|max:256"]);
        
        $key = new Key();
        $key->name = $request->input("name");
        $key->organization_id = $organization->id;
        $key->save();
        
        session()->flash("new_key", $key);
        
        return redirect(action("OrganizationController@show", ["organization" => $organization]));
    }
    
    public function destroy(Organization $organization, Key $key)
    {
        $this->authorize("update", $key->organization);
        $key->delete();
        return redirect(action("OrganizationController@show", ["organization" => $organization]));
    }
}
