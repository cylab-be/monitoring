<?php

namespace App\Http\Controllers;

use App\Rack;
use Illuminate\Http\Request;

class RackController extends Controller
{
    public function __construct()
    {
        // Uncomment to require authentication
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $organization = $this->organization();
        $this->authorize("show", $organization);
        return view("rack.index", ["organization" => $organization]);
    }

    public function dashboard()
    {
        $organization = $this->organization();
        $this->authorize("show", $organization);
        return view("rack.dashboard", ["organization" => $organization]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        $organization = $this->organization();
        return view("rack.edit", [
            "rack" => new Rack(),
            "organization" => $organization]);
    }

    public function edit(Rack $rack)
    {
        $organization = $this->organization();
        return view("rack.edit", [
            "rack" => $rack,
            "organization" => $organization]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $organization = $this->organization();
        $rack = new Rack();
        $rack->organization_id = $organization->id;
        return $this->save($request, $rack);
    }

    public function update(Rack $rack, Request $request)
    {
        return $this->save($request, $rack);
    }

    public function save(Request $request, Rack $rack)
    {
        $request->validate([
            "name" => "required|string",
            "height" => "required|integer|min:1|max:50"
        ]);

        $rack->name = $request->name;
        $rack->height = $request->height;
        $rack->save();

        return redirect(route("racks.index"));
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
