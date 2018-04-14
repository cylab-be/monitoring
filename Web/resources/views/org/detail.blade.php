@extends('layouts.app')

@section('content')
    <div class="py-5">
        <div class="container bg-light">
            <div class="row bg-primary">
                <div class="col-md-12">
                    <h3 class="display-5 text-light">{{ $organization->name }}</h3>
                </div>
            </div>
                {{ Form::open(array('url' => 'org/'.$organization->name )) }}
                Add a server : {{Form::text('token','Token')}}
                {{ Form::submit('Add a server')}}
                {{ Form::hidden('organization', $organization->name) }}
                 {{ Form::close() }}
            <div class="row">
                @foreach ($servers as $server)
                    <div class="col-md-3">
                        <div class="card border">
                            <div class="card-header">{{ $server->name }} <a class="text-dark" href="#"><i class="pull-right fa fa-lg fa-cog"></i></a></div>
                            <div class="card-body">
                                <h6 class="text-muted"><i class="fa d-inline fa-lg fa-exclamation-circle text-danger"></i>&nbsp;Reboot needed</h6>
                                <p><i class="fa d-inline fa-lg fa-circle text-success"></i>&nbsp;Free space ok</p>
                                @if ($server->sensors !=null)
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
                            </div>
                        </div>
                    </div>

                @endforeach
            </div>
            </div>
        </div>
@endsection
