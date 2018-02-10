@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
            Look at your organizations : <a href="org">
                            Organizations
                        </a>
                    @foreach($organization as $org)
                         <p> Organization : {{ $org->name }}</p>
                            @foreach ($org->servers as $server)
                                <p> Server : {{ $server->name }}</p>
                                @if ($server->lastState["content"] != null)

                                    <p> Last updated state:</p>

                                    @if($server->lastState["content"]->{"Reboot"})
                                        <p> ok</p>
                                    @else
                                        <p>not ok</p>
                                    @endif

                                @endif
                            @endforeach
                     @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
