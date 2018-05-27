@extends('layouts.app')

@section('content')
    <div class="py-5">
        <div class="container bg-light">
            <div class="row bg-primary">
                <div class="col-md-12">
                    <h3 class="display-5 text-light">Dashboard</h3>
                </div>
            </div>
            <div class="row my-1">
                <div class="col-md-12"><a class="text-muted text-xs-center" href="org">
                        Look at your organizations
                    </a>
                </div>
                @foreach($organization as $org)
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header"> {{ $org->name }}<a class="text-dark"
                                                                         href="{{ route('organization',$org->name) }}"><i
                                            class="pull-right fa fa-lg fa-cog"></i></a></div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">Server</th>
                                        <th scope="col">Disk</th>
                                        <th scope="col">Reboot</th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($org->servers as $server)
                                        <tr>
                                            <th scope="row">{{ $server->name }}</th>
                                            @if ($server->lastState["content"] != null)
                                                <td>{{$server->lastState["diskOk"]}}</td>
                                                <td>@if($server->lastState["content"]->{"Reboot"})
                                                        <p>ok</p>
                                                    @else
                                                        <p>not ok</p>
                                                    @endif</td>
                                                <td></td>
                                            @else
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            @endif
                                            <td><a class="btn btn-secondary" href="{{ route('serverDetails',$server->id) }}">Go to details</a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
