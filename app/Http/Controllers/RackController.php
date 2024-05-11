<?php

namespace App\Http\Controllers;

use App\Rack;
use App\Organization;
use Illuminate\Http\Request;

class RackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(Organization $organization)
    {
        $this->authorize("show", $organization);
        return view("organization.rack", ["organization" => $organization]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(Organization $organization)
    {
        return view("rack.edit", [
            "rack" => new Rack(),
            "organization" => $organization]);
    }
    
    public function edit(Organization $organization, Rack $rack)
    {
        return view("rack.edit", [
            "rack" => $rack,
            "organization" => $organization]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Organization $organization, Request $request)
    {
        return $this->save($organization, $request, new Rack());
    }
    
    public function update(Organization $organization, Rack $rack, Request $request)
    {
        return $this->save($organization, $request, $rack);
    }
    
    public function save(Organization $organization, Request $request, Rack $rack)
    {
        $request->validate([
            "name" => "required|string",
            "height" => "required|integer|min:1|max:50"
        ]);
        
        $rack->name = $request->name;
        $rack->height = $request->height;
        $rack->organization_id = $organization->id;
        $rack->save();
        
        return redirect(action("RackController@index", ["organization" => $organization]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rack  $rack
     */
    public function destroy(Rack $rack)
    {
        //
    }
}
