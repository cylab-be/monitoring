@extends('layouts.app')
@section('title', 'Devices | ' . $organization->name )

@section('content')
<h1>Devices</h1>
<p>
    <a href="{{ route('servers.create', ["organization" => $organization]) }}"
       class="btn btn-primary btn-sm">
        <i class="fa fa-plus-circle" aria-hidden="true"></i> New
    </a>
</p>

@include('server.partials.table', ["servers" => $organization->servers->sortBy("name")])
@endsection