@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Your organizations</div>
                @foreach ($organization as $org)
                    <p>{{ $org->name }}</p>
                @endforeach
                @foreach ($servers as $server)
                    <p>{{ $server->name }}</p>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
