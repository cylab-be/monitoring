@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    Users
                </div>

                <div class="card-body">
                    <ul class="list-unstyled">
                        @foreach ($organization->users as $user)
                        <li>{{ $user->name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Danger zone
                </div>
                <div class="card-body">
                    <a class="btn btn-primary"
                       href="{{ action('OrganizationController@edit', ['Organization' => $organization]) }}">
                         Edit
                    </a>

                    <form method="POST"
                          action="{{ action('OrganizationController@destroy', ['Organization' => $organization]) }}"
                          style="display: inline-block">
                        {{ csrf_field() }}
                        {{ method_field("DELETE") }}
                        <button class="btn btn-danger">
                             Delete
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <div class="col-md-9">

            <h1>{{ $organization->name }}</h1>

            <p>
                <a href="{{ action('ServerController@create') }}" class="btn btn-primary">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> New server
                </a>
            </p>
            <table class="table table-striped">
                <tr>
                    <th>Name</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                @foreach($organization->servers()->orderBy("name")->get() as $server)
                <tr>
                    <td>{{ $server->name }}</td>
                    <td>{!! $server->getBadge() !!}</td>
                    <td>{{ $server->lastRecordTime()->diffForHumans() }}</td>
                    <td>{{ $server->lsb() }}</td>
                    <td class="text-right">
                        <a class="btn btn-primary btn-sm"
                           href="{{ action('ServerController@show', ['Server' => $server]) }}">
                            <i class="fa fa-search" aria-hidden="true"></i> Show
                        </a>

                        <a class="btn btn-primary btn-sm"
                           href="{{ action('ServerController@edit', ['Server' => $server]) }}">
                            <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                        </a>

                        <form method="POST"
                              action="{{ action('ServerController@destroy', ['Server' => $server]) }}"
                              style="display: inline-block">
                            {{ csrf_field() }}
                            {{ method_field("DELETE") }}
                            <button class="btn btn-danger btn-sm">
                                <i class="fa fa-times-circle" aria-hidden="true"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
