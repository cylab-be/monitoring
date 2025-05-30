@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    <h1>{{ $organization->name }}</h1>

    <div class="row">
        @foreach($organization->devicesByStatus() as $server)
        <div class="col-md-3">
            <div class="card card-border-3 border-{{ $server->status()->color() }}">
                <div class="card-header">
                    <h5 class="card-title">
                        <a class="text-dark"
                           href="{{ $server->getUrlAttribute() }}">
                        {{ $server->name }}
                        </a>
                    </h5>
                </div>

                <div class="card-body">
                    <ul class="list-unstyled">
                        @foreach ($server->getSensorsNOK() as $report)
                        <li>{{ $report->title }}</li>
                        @endforeach
                    </ul>

                    <p class="card-text">
                        <small class="text-muted">
                            Last updated {{ $server->lastSummary()->time()->diffForHumans() }}
                        </small>
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="text-muted bottom-right-fixed">
    Reload in <span id="reload-countdown">300</span> seconds
</div>

<script type="text/javascript">
    var reload_countdown = 300;
    setInterval(function() {
        reload_countdown -= 1;
        $('#reload-countdown').text(reload_countdown);

        if (reload_countdown === 0) {
            console.log('reload...');
            location.reload();
        }
    }, 1000);
</script>
@endsection
