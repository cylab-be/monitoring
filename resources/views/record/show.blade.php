@extends('layouts.app')

@section('content')
<div class="container">
    <p>
        <span class="badge badge-secondary">@ {{ $record->id }}</span>
        <span class="badge badge-primary"># {{ $record->label }}</span>
        <a class="badge badge-primary"
           href="{{ action("ServerController@show", ["server" => $record->server]) }}">
            <i class="fas fa-desktop"></i> {{ $record->server->name }}
        </a>
        <span class="badge badge-secondary">
            <i class="far fa-clock"></i> {{ $record->time() }}
        </span>
    </p>

    <div class="card">
        <div class="card-body">
            <pre><code class="small">{{ $record->data }}</code></pre>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            Matching agents
            <table class="table">
                @foreach ($agents as $agent)
                <tr>
                    <td>
                        <a class=""
                           href="{{ route("records.run", ["record" => $record, "agent" => $agent->id()]) }}"
                           title="Run">
                            <i class="fas fa-forward"></i>
                            {{ $agent->name() }}
                        </a>
                    </td>
                    <td>{{ $agent->config()->description }}</td>

                </tr>
                @endforeach
            </table>
        </div>
    </div>

</div>
@endsection
