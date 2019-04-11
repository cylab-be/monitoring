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
            'name' => 'required|string|regex:/^[a-zA-Z0-9\s-\.]+$/|max:255'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
                "organization.index",
                array("organizations" => Auth::user()->organizations->sortBy("name")));
    }

    /**
     * Show the form for creating a new resource.
     * We use the same view for create and update => provide an empty Organization.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("organization.edit", ["organization" => new Organization()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $organization = new Organization();
        $organization->name = $request->name;
        Auth::user()->organizations()->save($organization);

        return redirect(action('OrganizationController@index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  Organization $organization     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        return view("organization.show", array("organization" => $organization));
    }

    public function dashboard(Organization $organization)
    {
        return view("organization.dashboard", array("organization" => $organization));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Organization $organization     * @return \Illuminate\Http\Response
     */
    public function edit(Organization $organization)
    {
        return view("organization.edit", array("organization" => $organization));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Organization $organization     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organization $organization)
    {
        $this->validator($request->all())->validate();

        $organization->name = $request->name;
        $organization->save();
        return redirect(action('OrganizationController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Organization::find($id)->delete();
        return redirect(action("OrganizationController@index"));
    }
}
