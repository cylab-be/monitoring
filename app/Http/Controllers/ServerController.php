<?php
namespace App\Http\Controllers;

use App\Server;
use App\Organization;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ServerController extends Controller
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
        return view("server.index", ["organization" => $organization]);
    }

    public function create(Organization $organization)
    {
        $server = new Server();
        $server->organization = $organization;
        $this->authorize("save", $server);
        return view("server.edit", ["server" => $server, "organization" => $organization]);
    }

    public function show(Server $server)
    {
        $this->authorize("show", $server);
        return view("server.show", ["server" => $server, "organization" => $server->organization]);
    }

    public function edit(Server $server)
    {
        $this->authorize("save", $server);
        return view("server.edit", ["server" => $server, "organization" => $server->organization]);
    }

    public function records(Server $server)
    {
        $this->authorize("show", $server);
        return view("server.records", [
            "server" => $server,
            "organization" => $server->organization,
            "records" => $server->records()->orderByDesc("id")->simplePaginate(100)]);
    }

    // ------------------ ACTIONS

    public function store(Request $request)
    {
        return $this->save($request, new Server());
    }

    public function update(Server $server, Request $request)
    {
        return $this->save($request, $server);
    }

    private function save(Request $request, Server $server)
    {
        $request->validate([
            'name' => 'required|string|regex:/^[a-zA-Z0-9\s\-\.]+$/|max:255',
            "organization_id" => Rule::in(Auth::user()->organizations->modelKeys()),
            "description" => 'nullable|string',
            "rack_id" => "nullable|integer",
            "size" => "nullable|int|min:0|max:48",
            "position" => "nullable|int|min:0|max:48"]);

        $server->name = $request->name;
        $server->organization()->associate(Organization::find($request->organization_id));
        $server->description = $request->description;

        // optional fields
        $server->size = $request->input("size", 0);
        $server->position = $request->input("position", 0);
        $server->rack_id = $request->rack_id;

        if ($server->rack_id == 0) {
            $server->rack_id = null;
        }

        if ($server->size == null) {
            $server->size = 0;
        }

        if ($server->position == null) {
            $server->position = 0;
        }

        $this->authorize("save", $server);
        $server->save();

        if (is_null($server->info)) {
            $server->info()->create();
        }

        return redirect(route("servers.show", ["server" => $server]));
    }

    public function destroy(Server $server)
    {
        $this->authorize("destroy", $server);

        // delete child DB records
        $server->changes()->delete();
        $server->records()->delete();
        $server->reports()->delete();
        $server->summaries()->delete();
        $server->delete();
        return redirect(route("organizations.show", ["organization" => $server->organization]));
    }
}
