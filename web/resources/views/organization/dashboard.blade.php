@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1>{{ $organization->name }}</h1>

    <div class="row">
        @foreach($organization->servers()->orderBy("name")->get() as $server)
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        {{ $server->name }}
                    </h5>
                </div>

                <div class="card-body">
                    <ul>
                        @foreach ($server->getSensors() as $sensor)
                        @if ($sensor->status() > 0)
                        <li>{{ $sensor->getName() }}</li>
                        @endif
                        @endforeach
                    </ul>

                    {!! $server->getBadge() !!}
                    <p>
                        {{ $server->lastRecordTime()->diffForHumans() }}
                    </p>
                </div>

                <div class="card-footer">
                    <a class="btn btn-primary btn-sm"
                       href="{{ action("ServerController@show", ["server" => $server]) }}">
                        View
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
