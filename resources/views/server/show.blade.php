@extends('layouts.app')

@section('content')
<script src="/js/sensors.js"></script>

<div class="container">

    <h1>
        <a class="text-decoration-none"
            href="{{ route("servers.index", ["organization" => $server->organization]) }}">{{ $server->organization->name }}</a>
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
            </p>

            <p>
                <i class="fas fa-heart"></i> {{ $server->info->kernel }}
                <i class="fas fa-terminal ml-3"></i> {{ $server->info->lsb }}
            </p>

            <p>
                @foreach ($server->tags as $tag)
                <a class="badge badge-primary"
                   href="{{ route("tags.show", ["tag" => $tag]) }}">
                    {{ $tag->name }}
                </a>
                @endforeach
            </p>

            @if ($server->info->uuid)
            <p>
                <i class="far fa-id-card"></i> {{ $server->info->uuid }}
            </p>
            @endif

            <p>
                <i class="fas fa-network-wired"></i>
                @foreach ($server->info->addresses() as $address)
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

            <p class="mb-0">
                <a class="btn btn-sm btn-secondary"
                   href="{{ action("ServerController@records", ["server" => $server]) }}">
                    <i class="fas fa-search"></i> Inspect records
                </a>
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {!! $server->descriptionAsHTML() !!}

            <div class="bottom-right">
                <a href="{{ action('ServerController@edit', ['server' => $server, "organization" => $server->organization]) }}"><i class="fas fa-edit"></i></a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Manual IP addresses</div>
        <div class="card-body">
            <table class="table table-sm">
                @foreach ($server->ips as $ip)
                <tr>
                    <td>{{ $ip->ip }}</td>
                    <td>{{ $ip->comment }}</td>
                    <td class="text-right">
                        <form method="POST"
                              class="d-inline-block"
                              action="{{ route('ips.destroy', ['ip' => $ip]) }}">
                            {{ csrf_field() }}
                            {{ method_field("DELETE") }}
                            <button class="btn btn-danger btn-sm">
                                <i class="fa fa-times-circle" aria-hidden="true"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </table>

            <a class="btn btn-primary btn-sm"
                href="{{ route("ips.create", ["server" => $server]) }}">
                   New
            </a>
        </div>
    </div>

    @include("server.partials.tags")

    @foreach ($server->lastSummary()->reports() as $report)
    @include("report.partials.show", ["report" => $report])
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
                   href="{{ action('ServerController@edit', ['server' => $server, "organization" => $server->organization]) }}">
                    <i class="fas fa-pencil-alt"></i> Edit
                </a>

                <form method="POST"
                      action="{{ action('ServerController@destroy', ['server' => $server, "organization" => $server->organization]) }}"
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
