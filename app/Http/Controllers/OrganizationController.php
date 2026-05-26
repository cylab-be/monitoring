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

    /**
     * Networks view
     * @param Organization $organization
     */
    public function networks(Organization $organization)
    {
        $this->authorize("show", $organization);

        $networks = [];
        foreach ($organization->servers as $server) {
            foreach ($server->info->addresses as $address) {
                // for now I assume 24 bits subnet masks
                $network = $this->extractSubnet($address, 24);
                $networks[$network][$address] = $server;
            }
        }
        $this->natksortRecursive($networks);
        return view("organization.networks", ["networks" => $networks]);
    }

    public function natksortRecursive(&$arr)
    {
        ksort($arr, SORT_NATURAL);
        foreach ($arr as &$subarr) {
            if (is_array($subarr)) {
                $this->natksortRecursive($subarr);
            }
        }
    }

    /**
     * Extract subnet address from IP and mask length
     * E.g. extractSubnet("192.168.178.2", 24) => "192.168.178.0"
     *
     * @param string $ip
     * @param int $len
     * @return string
     */
    private function extractSubnet(string $ip, int $len) : string
    {
        #list($ip, $len) = explode('/', $ip);

        $ip_long = ip2long($ip);
        $subnet_mask = (0xffffffff >> (32 - $len)) << (32 - $len);
        $subnet = long2ip($ip_long & $subnet_mask);
        return $subnet;
    }
}
