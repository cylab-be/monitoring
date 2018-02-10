@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Organization : {{ $organization->name }}</div>
                @foreach ($servers as $server)
                    <p> Server : {{ $server->name }}</p>
                    @if ($server->sensors !=null)
                        <p> Sensors :}</p>
                        @foreach($server->sensors as $sensor){
                        <p> {{$sensor["content"]->{"TCP"} }}</p>
                        <p> {{$sensor["content"]->{"UDP"} }}</p>
                        <p> {{$sensor["content"]->{"Network"} }}</p>
                        <p> {{$sensor["content"]->{"Inodes"} }}</p>
                        @if($sensor["content"]->{"Reboot"})
                             <p> ok</p>
                        @else
                            <p>not ok</p>
                        @endif
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
