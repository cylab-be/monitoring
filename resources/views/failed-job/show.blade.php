@extends('layouts.app')
@section('title', 'Failed job #' . $job->id)

@section('content')
<div class="container">
    <h1>Failed job #{{ $job->id }}</h1>
            
    <div class="card">
        <div class='card-header'>
            Command
        </div>
        <div class="card-body">
            <pre><code class="small">{!! json_encode(unserialize($job->payload->data->command, ["allowed_classes" => false]), JSON_PRETTY_PRINT) !!}</code></pre>
        </div>    
    </div>
            
    
    <div class="card">
        <div class='card-header'>
            Exception
        </div>
        <div class="card-body">
            <pre><code class="small">{{ $job->exception }}</code></pre>
        </div>
    </div>
</div>
@endsection
