@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Organization : {{ $organization->name }}</div>
                @foreach ($servers as $server)
                    <p> Server : {{ $server->name }}</p>

                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
