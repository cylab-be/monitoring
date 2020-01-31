@extends('layouts.app')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/0.5.7/chartjs-plugin-annotation.min.js"></script>
<script>
window.chartColors = {
    red: 'rgba(255, 99, 132, 0.2)',
    orange: 'rgba(255, 165, 0, 0.3)',
    yellow: 'rgba(255, 205, 86, 0.2)',
    green: 'rgba(0, 178, 0, 0.3)',
    blue: 'rgba(54, 162, 235, 0.2)',
    purple: 'rgba(153, 102, 255, 0.2)',
    grey: 'rgba(201, 203, 207, 0.2)'
};

window.colorNames = Object.keys(window.chartColors);

window.monitorURL = "{{ url('/') }}";
window.monitorServerID = {{ $server->id }};
window.monitorServerToken = "{{ $server->read_token }}";
</script>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <p>{!! $server->statusBadge() !!}</p>

                    <p>
                        Last heartbeet:<br>
                        {{ $server->lastRecordTime()->toDateTimeString() }}<br>
                        ({{ $server->lastRecordTime()->diffForHumans() }})
                    </p>

                    <p>Client version: {{ $server->clientVersion() }}</p>

                    <p>Uptime: {{ $server->uptime() }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <p>{{ $server->manufacturer() }} {{ $server->productName() }}</p>
                    <p><small>{{ $server->uuid() }}</small></p>

                    @php
                    $cpuinfo = $server->cpuinfo();
                    @endphp
                    @if ($cpuinfo !== null)
                    <p>{{ $cpuinfo["cpu"] }} ({{ $cpuinfo["threads"] }} threads)</p>
                    @endif
                    <p>Memory: {{ $server->meminfo() }}</p>
                    <p>{{ $server->lsb() }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Danger zone
                </div>

                <div class="card-body">
                    <a class="btn btn-primary btn-sm"
                       href="{{ action('ServerController@edit', ['Server' => $server]) }}">
                        <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                    </a>

                    <form method="POST"
                          action="{{ action('ServerController@destroy', ['Server' => $server]) }}"
                          style="display: inline-block">
                        {{ csrf_field() }}
                        {{ method_field("DELETE") }}
                        <button class="btn btn-danger btn-sm">
                            <i class="fa fa-times-circle" aria-hidden="true"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h1>
                <a href="{{ $server->organization->url() }}">{{ $server->organization->name }}</a>
                / {{ $server->name }}
            </h1>

            @foreach ($server->getSensors() as $sensor)
            <div class="card">
                <div class="card-header">
                    {{ $sensor->getName() }}

                    <div class="float-right">
                        {!! $sensor->getBadge() !!}
                    </div>
                </div>
                <div class="card-body">
                    {!! $sensor->reportHTML() !!}
                </div>
            </div>
            @endforeach

            <div class="card">
                <div class="card-header">
                    History
                </div>
                <div class="card-body">
                    <table class='table table-sm'>
                        @foreach($server->getChanges() as $change)
                        <tr>
                            <td>{{ $change->getTimeCarbon()->toDateTimeString() }}</td>
                            <td>{!! $change->getStatusBadge() !!}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>

             <div class="card">
                <div class="card-header">
                    PHP Client installation
                </div>
                <div class="card-body">
                    <p>Download client application:</p>
                    <pre style="font-size: 75%; overflow: hidden"><code>wget {{ $server->lastClientUrl() }}
unzip monitor-*.zip
</code></pre>
                    <p>Move it to /usr/bin:</p>
                    <pre style="font-size: 75%; overflow: hidden"><code>sudo mv monitor /usr/bin/monitor
</code></pre>

                    <p>Test it:</p>
                    <pre style="font-size: 75%; overflow: hidden"><code>sudo monitor ping -i {{ $server->id }} -t {{ $server->token }}
</code></pre>

                    <p>Add a cron entry to run it automatically:</p>
                    <pre style="font-size: 75%; overflow: hidden"><code>echo "*/5 * * * * root /usr/bin/monitor ping -i {{ $server->id }} -t {{ $server->token }}" | \
sudo tee -a /etc/crontab
            </code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
