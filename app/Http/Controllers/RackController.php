<?php

namespace App\Http\Controllers;

use App\Rack;
use App\Organization;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class RackController extends Controller
{
    public function __construct()
    {
        // Uncomment to require authentication
        $this->middleware('auth');
    }

    // ------------------ VIEWS

    public function index(Organization $organization)
    {
        $this->authorize("show", $organization);
        return view("rack.index", ["organization" => $organization]);
    }

    public function create(Organization $organization)
    {
        $this->authorize("update", $organization);
        $rack = new Rack();
        $rack->organization()->associate($organization);
        return view("rack.edit", ["rack" => $rack, "organization" => $organization]);
    }

    public function edit(Rack $rack)
    {
        $this->authorize("update", $rack->organization);
        return view("rack.edit", ["rack" => $rack, "organization" => $rack->organization]);
    }

    public function dashboard(Organization $organization)
    {
        $this->authorize("show", $organization);
        return view("rack.dashboard", ["organization" => $organization]);
    }

    // ------------------ ACTIONS

    public function store(Request $request)
    {
        return $this->save(new Rack(), $request);
    }

    public function update(Rack $rack, Request $request)
    {
        return $this->save($rack, $request);
    }

    public function save(Rack $rack, Request $request)
    {

        $request->validate([
            "name" => "required|string|max:255",
            "organization_id" => Rule::in(Auth::user()->organizations->modelKeys()),
            "height" => "required|integer|min:1|max:50"
        ]);

        $rack->name = $request->name;
        $rack->height = $request->height;
        $rack->organization()->associate(Organization::find($request->organization_id));

        $this->authorize("update", $rack->organization);
        $rack->save();

        return redirect(route("racks.index", ["organization" => $rack->organization]));
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
