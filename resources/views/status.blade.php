@extends('layouts.app')

@php
$scheduler = \App\AgentScheduler::get();
@endphp

@section('title', 'Status')

@section('content')
<div class="container">
    <h1>Status</h1>

    <h2>Jobs</h2>
    <p>Analysis jobs in queue: {{ Queue::size() }}</p>
    <p>
        Throttling threshold: {{ $scheduler->throttlingTreshold() }}
    </p>
    <p class="text-muted">
        <i class="fas fa-question-circle"></i> When queue size reaches threshold, any new analysis job will be discarded
        until the queue size falls below threshold.
    </p>

    <h2>Database</h2>
    <p>Servers : {{ \App\Server::count() }}</p>
    <p>Records : {{ \App\Record::count() }}</p>
    <p>Reports : {{ \App\Report::count() }}</p>

    <h2>Agents</h2>
    <p>Autodiscovered <b>{{ $scheduler->sensors()->count() }}</b> analysis agents</p>

    <table class="table table-sm">
        @foreach ($scheduler->sensors() as $sensor)
        <tr>
            <td>{{ get_class($sensor) }}</td>
            <td>{{ Str::limit($sensor->config()->description, 100) }}</td>
        </tr>
        @endforeach
    </table>
</div>
@endsection