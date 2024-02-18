@extends('layouts.app')

@section('content')
<div class="container">
    
    <p>
        <span class="badge badge-secondary">#{{ $record->id }}</span>
        <span class="badge badge-primary">{{ $record->label }}</span>
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

</div>
@endsection
