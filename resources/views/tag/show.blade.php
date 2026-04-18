@extends('layouts.app')
@section('title', $tag->name . ' | ' . $organization->name )

@section('content')
<div class="container">
    <h1>{{ $tag->name }}</h1>

    @include('server.partials.table', ["servers" => $tag->servers->sortBy('name')])
</div>
@endsection