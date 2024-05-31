@extends('layouts.app')

@section('content')
<script>
window.monitorURL = "{{ url('/') }}";
window.monitorServerID = {{ $server->id }};
window.monitorServerToken = "{{ $server->read_token }}";
</script>
<script src="/js/sensors.js"></script>

<div class="container">
    
    <h1>
        <a class="text-decoration-none"
            href="{{ $server->organization->url() }}">{{ $server->organization->name }}</a>
        / {{ $server->name }}
    </h1>
    
    <div class="card">
        <div class="card-body">
            <p>
                {!! $server->status()->badge() !!}
                {{ $server->lastSummary()->time()->toDateTimeString() }}
                ({{ $server->lastSummary()->time()->diffForHumans() }})
            </p>
            
            <p>
                <i class="fas fa-microchip"></i> {{ $server->info->cpuName() }} | {{ $server->info->vCores() }} vCores
                <i class="fas fa-memory ml-3"></i> {{ $server->info->memoryTotalForHumans() }}
                <i class="fas fa-server ml-3"></i> {{ $server->info->manufacturer }} {{ $server->info->product }}
                <i class="fas fa-terminal ml-3"></i> {{ $server->info->lsb }}
            </p>
            
            @if ($server->info->uuid)
            <p>
                <i class="far fa-id-card"></i> {{ $server->info->uuid }}
            </p>
            @endif
            
            <p>
                <i class="fas fa-network-wired"></i>
                @foreach ($server->info->addresses as $address)
                {{ $address }}
                <a class="badge badge-primary" href="ssh://{{ $address }}">ssh</a>
                <a class="badge badge-primary" target="_blanck" href="http://{{ $address }}">http</a> 
                @endforeach
                
                <a href="https://cylab.be/blog/318/create-a-handler-for-opening-special-urls-like-ssh">
                    <i class="fas fa-question-circle"></i>
                </a>
            </p>

            <p>Uptime: {{ $server->info->uptimeForHumans() }}</p>
            
            @if (! is_null($server->rack_id))
            <p>
                Rack
                <b>
                    {{ $server->rack->name }}#{{ $server->position }} 
                    [{{ $server->size }}u]
                </b>
            </p>
            @endif
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            {!! $server->descriptionAsHTML() !!}
            
            <div class="bottom-right">
                <a href="{{ action('ServerController@edit', ['server' => $server]) }}"><i class="fas fa-edit"></i></a>
            </div>
        </div>
    </div>

    @foreach ($server->lastSummary()->reports() as $report)
    <div class="card">
        <div class="card-header">
            {{ $report->title() }}

            <div class="float-right">
                <a class="badge badge-secondary"
                   href="{{ action("RecordController@show", ["record" => $report->record_id]) }}">
                    <i class="fas fa-search"></i>
                </a>
                {!! $report->status()->badge() !!}
            </div>
        </div>
        <div class="card-body">
            {!! $report->html() !!}
        </div>
    </div>
    @endforeach

    <div class="card">
        <div class="card-header">
            History
        </div>
        <div class="card-body">
            <table class='table table-sm'>
                @foreach($server->lastChanges() as $change)
                <tr>
                    <td>{{ $change->getTimeCarbon()->toDateTimeString() }}</td>
                    <td class="text-right">{!! $change->status()->badge() !!}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    @include("server.partials.client-install")
    
    <div class="card">
        <div class="card-header">
            Danger zone
        </div>

        <div class="card-body">
            <p>Server id: <code>{{ $server->id }}</code></p>
            <p>Token: <code>{{ $server->token }}</code></p>

            <div>
                <a class="btn btn-primary btn-sm"
                   href="{{ action('ServerController@edit', ['server' => $server]) }}">
                    <i class="fas fa-pencil-alt"></i> Edit
                </a>

                <form method="POST"
                      action="{{ action('ServerController@destroy', ['server' => $server]) }}"
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
</div>
@endsection
