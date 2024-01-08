@extends('layouts.app')

@section('content')
<div class="container">

    <h1>{{ $organization->name }}</h1>

    <p>
        <a href="{{ action('ServerController@create') }}" 
           class="btn btn-primary btn-sm">
            <i class="fa fa-plus-circle" aria-hidden="true"></i> New server
        </a>
        <a class="btn btn-primary btn-sm"
           href="{{ action("OrganizationController@dashboard",
                       ["organization" => $organization]) }}">
            Dashboard <i class="fas fa-lock ml-2"></i>
        </a>
        <a class="btn btn-primary btn-sm"
           href="{{ route("organization.public.dashboard", [
               "organization" => $organization,
                "token" => $organization->dashboard_token]) }}">
            Public dashboard <i class="fas fa-globe ml-2"></i>
        </a>
        <a class="btn btn-primary btn-sm"
           href="{{ action("OrganizationController@resetToken", [
               "organization" => $organization]) }}">
            <i class="fas fa-redo-alt"></i> Reset dashboard token
        </a>
    </p>
    
    <table class="table table-striped my-5">
        @foreach($organization->servers->sortBy("name") as $server)
        <tr>
            <td>
                <a class="text-decoration-none h5"
                   href="{{ action('ServerController@show', ['server' => $server]) }}">
                    {{ $server->name }}
                </a><br>
                
                <small class="text-muted">
                    <i class="fas fa-microchip"></i> {{ $server->info()->vCores() }} vCores
                    <i class="fas fa-memory ml-3"></i> {{ $server->info()->memoryTotalForHumans() }}
                    <i class="fas fa-server ml-3"></i> {{ $server->info()->manufacturer() }} {{ $server->info()->productName() }}
                    <i class="fas fa-terminal ml-3"></i> {{ $server->info()->lsb() }}
                    <i class="fas fa-network-wired ml-3"></i>
                    @foreach ($server->info()->addresses() as $address)
                    <a href="ssh://{{ $address }}">{{ $address }}</a> 
                    @endforeach
                </small>
                
            </td>
            <td></td>
            <td class="text-right">
                {!! $server->status()->badge() !!}
                {{ $server->info()->lastRecordTime()->diffForHumans() }}
            </td>
        </tr>
        @endforeach
    </table>

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

            <p>
                <a class="btn btn-primary btn-sm"
                   href="{{ action("OrganizationUserController@create", ["organization" => $organization]) }}">
                    Invite user to organization
                </a>
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Danger zone
        </div>
        <div class="card-body">
            <a class="btn btn-primary"
               href="{{ action('OrganizationController@edit', ['organization' => $organization]) }}">
                 Edit
            </a>

            <form method="POST"
                  action="{{ action('OrganizationController@destroy', ['organization' => $organization]) }}"
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
@endsection
