@extends('layouts.app')
@section('title', $subnet->name . ' | ' . $organization->name )

@section('content')
<h1>{{ $subnet->name }}</h1>

<p>
    <span class="btn btn-sm btn-primary">
        <i class="fas fa-network-wired"></i>{{ $subnet->address }}/{{ $subnet->mask }}
    </span>
</p>

@include('server.partials.table')
@endsection