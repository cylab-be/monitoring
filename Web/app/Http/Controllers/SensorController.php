<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensors;

class SensorController extends Controller
{
    public function index()
    {
        return Sensors::all();
    }

    public function show($id)
    {
        return Sensors::find($id);
    }

    public function store(Request $request)
    {
        return Sensors::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $sensor = Sensors::findOrFail($id);
        $sensor->update($request->all());

        return $sensor;
    }

    public function delete(Request $request, $id)
    {
        $sensor = Sensors::findOrFail($id);
        $sensor->delete();

        return 204;
    }

}
