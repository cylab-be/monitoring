@extends('layouts.app')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/0.5.7/chartjs-plugin-annotation.min.js"></script>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <p>
                        Last heartbeet:<br>
                        {{ $server->lastRecordTime()->toDateTimeString() }}<br>
                        ({{ $server->lastRecordTime()->diffForHumans() }})
                    </p>

                    <p>Status: {{ $server->statusString() }}</p>
                    <p>Client version: {{ $server->clientVersion() }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
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
            <h1>{{ $server->name }}</h1>

            @foreach ($server->getSensors() as $sensor)
            <div class="card">
                <div class="card-header">
                    {{ get_class($sensor) }}

                    <div class="float-right">
                        {!! $server->getBadge() !!}
                    </div>
                </div>
                <div class="card-body">
                    {!! $sensor->report() !!}
                </div>
            </div>
            @endforeach

            <h3>PHP Client installation</h3>
            <pre style="font-size: 75%; background: #ddd; overflow: hidden"><code>
wget https://gitlab.cylab.be/cylab/monitoring/raw/master/php-client/bin/monitor.phar
sudo mv monitor.phar /usr/bin/monitor
sudo chmod +x /usr/bin/monitor
echo "*/5 * * * *   root    /usr/bin/monitor ping -i {{ $server->id }} -t {{ $server->token }}" | sudo tee -a /etc/crontab
            </code></pre>
        </div>

    </div>
</div>
@endsection
