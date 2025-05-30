<?php

namespace App\Http\Controllers;

use App\Subnet;
use App\Organization;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SubnetController extends Controller
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
        return view("subnet.index", ["organization" => $organization]);
    }

    public function create(Organization $organization)
    {
        $this->authorize("update", $organization);
        $subnet = new Subnet();
        $subnet->organization()->associate($organization);
        return view("subnet.edit", [
            "subnet" => $subnet,
            "organization" => $organization]);
    }

    public function show(Subnet $subnet)
    {
        $this->authorize("show", $subnet->organization);
        return view("subnet.show", [
            "subnet" => $subnet,
            "organization" => $subnet->organization]);
    }


    public function edit(Subnet $subnet)
    {
        $this->authorize("update", $subnet->organization);
        return view("subnet.edit", [
            "subnet" => $subnet,
            "organization" => $subnet->organization]);
    }

    /**
     * Vizualize subnets and devices in a graph.
     */
    public function view(Organization $organization)
    {
        $this->authorize("show", $organization);
        return view("subnet.view", ["organization" => $organization]);
    }

    // ------------------ ACTIONS

    public function store(Request $request)
    {
        return $this->save(new Subnet(), $request);
    }

    public function update(Subnet $subnet, Request $request)
    {
        return $this->save($subnet, $request);
    }

    public function save(Subnet $subnet, Request $request)
    {

        $request->validate([
            "name" => "required|string|max:255",
            "address" => "required|ip",
            "mask" => "required|int|min:0|max:32",
            "organization_id" => Rule::in(Auth::user()->organizations->modelKeys())
        ]);

        $subnet->name = $request->name;
        $subnet->address = $request->address;
        $subnet->mask = $request->mask;
        $subnet->organization()->associate(Organization::find($request->organization_id));

        $this->authorize("update", $subnet->organization);
        $subnet->save();

        return redirect(route("subnets.index", ["organization" => $subnet->organization]));
    }

    public function destroy(Subnet $subnet)
    {
        //
    }
}
