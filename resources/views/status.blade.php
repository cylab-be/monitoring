@extends('layouts.app')

@section('title', 'Status')

@section('content')
<div class="container">
    <h1>Status</h1>

    <h2>Jobs</h2>
    <p>Jobs in queue: {{ Queue::size() }}</p>

    <h2>Database</h2>
    <p>Servers : {{ \App\Server::count() }}</p>
    <p>Records : {{ \App\Record::count() }}</p>
    <p>Reports : {{ \App\Report::count() }}</p>
</div>
@endsection