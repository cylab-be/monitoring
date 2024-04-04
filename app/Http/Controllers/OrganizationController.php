<?php
namespace App\Http\Controllers;

use App\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{

    public function __construct()
    {
        // Uncomment to require authentication
        $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|regex:/^[a-zA-Z0-9\s\-\.]+$/|max:255'
        ]);
    }


    public function index()
    {
        $this->authorize("index", Organization::class);
        return view(
            "organization.index",
            array("organizations" => Auth::user()->organizations->sortBy("name"))
        );
    }

    /**
     * Show the form for creating a new resource.
     * We use the same view for create and update => provide an empty Organization.
     *
     */
    public function create()
    {
        $this->authorize("create", Organization::class);
        return view("organization.edit", ["organization" => new Organization()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $this->authorize("create", Organization::class);
        $this->validator($request->all())->validate();

        $organization = new Organization();
        $organization->name = $request->name;
        $organization->dashboard_token = \str_random(20);
        Auth::user()->organizations()->save($organization);

        return redirect(action('OrganizationController@index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  Organization $organization
     */
    public function show(Organization $organization)
    {
        $this->authorize("show", $organization);
        return view("organization.show", array("organization" => $organization));
    }

    public function dashboard(Organization $organization)
    {
        $this->authorize("show", $organization);
        return view("organization.dashboard", ["organization" => $organization]);
    }

    public function resetToken(Organization $organization)
    {
        $this->authorize("update", $organization);
        $organization->dashboard_token = \str_random(20);
        $organization->save();
        return redirect(action('OrganizationController@show', ["organization" => $organization]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Organization $organization
     */
    public function edit(Organization $organization)
    {
        $this->authorize("update", $organization);
        return view("organization.edit", array("organization" => $organization));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Organization $organization
     */
    public function update(Request $request, Organization $organization)
    {
        $this->authorize("update", $organization);
        $this->validator($request->all())->validate();

        $organization->name = $request->name;
        $organization->save();
        return redirect(action('OrganizationController@index'));
    }


    public function destroy(Organization $organization)
    {
        $this->authorize("destroy", $organization);
        $organization->delete();
        return redirect(action("OrganizationController@index"));
    }
}
