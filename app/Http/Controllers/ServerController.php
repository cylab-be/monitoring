<?php
namespace App\Http\Controllers;

use App\Server;

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

    private function rules() : array
    {
        return [
            'name' => 'required|string|regex:/^[a-zA-Z0-9\s\-\.]+$/|max:255',
            "organization_id" => Rule::in(Auth::user()->organizations->modelKeys()),
            "description" => 'nullable|string',
            "rack_id" => "nullable|integer",
            "size" => "nullable|int|min:0|max:48",
            "position" => "nullable|int|min:0|max:48"];
    }

    /**
     * Show the form for creating a new resource.
     * We use the same view for create and update => provide an empty Server.
     *
     */
    public function create()
    {
        $this->authorize("create", Server::class);
        
        $server = new Server();
        $server->organization = Auth::user()->organizations->first();
        return view("server.edit", ["server" => $server]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $this->authorize("create", Server::class);
        return $this->saveAndRedirect($request, new Server());
    }

    /**
     * Display the specified resource.
     *
     * @param  Server $server
     */
    public function show(Server $server)
    {
        $this->authorize("show", $server);
        return view("server.show", ["server" => $server]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Server $server
     */
    public function edit(Server $server)
    {
        $this->authorize("update", $server);
        return view("server.edit", array("server" => $server));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Server $server
     */
    public function update(Request $request, Server $server)
    {
        $this->authorize("update", $server);
        return $this->saveAndRedirect($request, $server);
    }

    private function saveAndRedirect(Request $request, Server $server)
    {
        $request->validate($this->rules());

        $server->name = $request->name;
        $server->organization_id = $request->organization_id;
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
        
        $server->save();

        return redirect(action("ServerController@show", ["server" => $server]));
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
        return redirect(action("OrganizationController@show", ["organization" => $server->organization]));
    }
}
