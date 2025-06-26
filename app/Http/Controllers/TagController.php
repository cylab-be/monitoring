<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Organization;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{

    public function __construct()
    {
        // Uncomment to require authentication
        $this->middleware('auth');
    }

    public function index(Organization $organization)
    {
        $this->authorize("show", $organization);
        return view("tag.index", ["organization" => $organization]);
    }

    public function create(Organization $organization)
    {
        $this->authorize("update", $organization);
        $tag = new Tag();
        $tag->organization()->associate($organization);
        return view("tag.edit", [
            "tag" => $tag,
            "organization" => $organization]);
    }

    public function show(Tag $tag)
    {
        $this->authorize("show", $tag->organization);
        return view("tag.show", [
            "tag" => $tag,
            "organization" => $tag->organization]);
    }


    public function edit(Tag $tag)
    {
        $this->authorize("update", $tag->organization);
        return view("tag.edit", [
            "tag" => $tag,
            "organization" => $tag->organization]);
    }


    // ------------------ ACTIONS

    public function store(Request $request)
    {
        return $this->save(new Tag(), $request);
    }

    public function update(Tag $tag, Request $request)
    {
        return $this->save($tag, $request);
    }

    public function save(Tag $tag, Request $request)
    {

        $request->validate([
            "name" => "required|string|max:255",
            "organization_id" => Rule::in(Auth::user()->organizations->modelKeys())
        ]);

        $tag->name = $request->name;
        $tag->organization()->associate(Organization::find($request->organization_id));

        $this->authorize("update", $tag->organization);
        $tag->save();

        return redirect(route("tags.index", ["organization" => $tag->organization]));
    }

    public function destroy(Tag $tag)
    {
        //
    }
}
