@extends('layouts.app')

@section('content')
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

                    <p>Status: {{ $server->status() }}</p>
                    <p>Client version: {{ $server->clientVersion() }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Danger zone</h4>
                </div>

                <div class="card-body">
                    <a class="btn btn-primary"
                       href="{{ action('ServerController@edit', ['Server' => $server]) }}">
                        <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                    </a>

                    <form method="POST"
                          action="{{ action('ServerController@destroy', ['Server' => $server]) }}"
                          style="display: inline-block">
                        {{ csrf_field() }}
                        {{ method_field("DELETE") }}
                        <button class="btn btn-danger">
                            <i class="fa fa-times-circle" aria-hidden="true"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h1>{{ $server->name }}</h1>

            @foreach ($server->getSensors() as $sensor)
            {!! $sensor->report() !!}
            @endforeach

            <h3>PHP Client installation</h3>
            <pre style="font-size: 75%; background: #ddd; overflow: hidden"><code>
wget https://gitlab.cylab.be/cylab/monitoring/raw/master/php-client/bin/monitor.phar
sudo mv monitor.phar /usr/bin/monitor
sudo chmod +x /usr/bin/monitor
sudo echo "*/5 * * * *   root    /usr/bin/monitor ping -i {{ $server->id }} -t {{ $server->token }}" >> /etc/crontab
            </code></pre>
        </div>

    </div>
</div>
@endsection
