@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    <h1>{{ $organization->name }}</h1>

    <div class="row">
        @foreach($organization->servers()->orderBy("name")->get() as $server)
        <div class="col-md-3">
            <div class="card card-border-3 border-{{ $server->color() }}">
                <div class="card-header">
                    <h5 class="card-title">
                        {{ $server->name }}
                    </h5>
                </div>

                <div class="card-body">
                    <ul class="list-unstyled">
                        @foreach ($server->getSensorsNOK() as $sensor)
                        <li>{{ $sensor->getName() }}</li>
                        @endforeach
                    </ul>

                    <p class="card-text">
                        <small class="text-muted">
                            Last updated {{ $server->lastRecordTime()->diffForHumans() }}
                        </small>
                    </p>
                </div>

                <div class="card-footer">
                    <a class="btn btn-secondary btn-sm"
                       href="{{ action("ServerController@show", ["server" => $server]) }}">
                        View
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script type="text/javascript">
    // Reload page every minute
    setTimeout(location.reload.bind(location), 300000);
</script>
@endsection
