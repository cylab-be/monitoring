<?php

namespace App\Http\Controllers;

use App\Subnet;
use Illuminate\Http\Request;

class SubnetController extends Controller
{
    public function __construct()
    {
        // Uncomment to require authentication
        $this->middleware('auth');
    }

    public function index()
    {
        $organization = $this->organization();
        $this->authorize("show", $organization);
        return view("subnet.index", ["organization" => $organization]);
    }
    
    /**
     * Vizualize subnets and devices in a graph.
     */
    public function view()
    {
        $organization = $this->organization();
        $this->authorize("show", $organization);
        return view("subnet.view", ["organization" => $organization]);
    }
    
    public function show(Subnet $subnet)
    {
        $organization = $this->organization();
        $this->authorize("show", $organization);
        return view("subnet.show", [
            "subnet" => $subnet,
            "organization" => $organization]);
    }

    public function create()
    {
        $organization = $this->organization();
        $this->authorize("update", $organization);
        return view("subnet.edit", [
            "subnet" => new Subnet(),
            "organization" => $organization]);
    }

    public function edit(Subnet $subnet)
    {
        $organization = $this->organization();
        $this->authorize("update", $organization);
        return view("subnet.edit", [
            "subnet" => $subnet,
            "organization" => $organization]);
    }

    public function store(Request $request)
    {
        $organization = $this->organization();
        $this->authorize("update", $organization);
        
        $subnet = new Subnet();
        $subnet->organization_id = $organization->id;
        return $this->save($request, $subnet);
    }

    public function update(Subnet $subnet, Request $request)
    {
        $this->authorize("update", $subnet->organization);
        return $this->save($request, $subnet);
    }

    public function save(Request $request, Subnet $subnet)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "address" => "required|ip",
            "mask" => "required|int|min:0|max:32"
        ]);

        $subnet->name = $request->name;
        $subnet->address = $request->address;
        $subnet->mask = $request->mask;
        $subnet->save();

        return redirect(route("subnets.index"));
    }

    public function destroy(Subnet $subnet)
    {
        //
    }
}
