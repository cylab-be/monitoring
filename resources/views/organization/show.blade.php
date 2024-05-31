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
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <a class="btn btn-primary btn-sm"
           href="{{ action("OrganizationDashboardController@json", [
               "organization" => $organization,
               "token" => $organization->dashboard_token]) }}">
            <i class="fas fa-code"></i> JSON
        </a>
        
        <a class="btn btn-primary btn-sm"
           href="{{ action("OrganizationController@resetToken", [
               "organization" => $organization]) }}">
            <i class="fas fa-redo-alt"></i> Reset dashboard token
        </a>
        
        <a class="btn btn-primary btn-sm"
           href="{{ action("RackController@index", ["organization" => $organization]) }}">
            <i class="fas fa-server"></i> Rack view
        </a>
        
        <a href="{{ action("RackController@create", ["organization" => $organization]) }}" 
           class="btn btn-primary btn-sm">
            <i class="fa fa-plus-circle" aria-hidden="true"></i> New rack
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
                
                <div class="small text-muted">
                <i class="fas fa-microchip"></i> {{ $server->info->cpuName() }} | {{ $server->info->vCores() }} vCores
                <i class="fas fa-memory ml-3"></i> {{ $server->info->memoryTotalForHumans() }}<br>
                
                <i class="fas fa-server"></i> {{ $server->info->manufacturer }} {{ $server->info->product }}
                <i class="fas fa-terminal ml-3"></i> {{ $server->info->lsb }}<br>
            
                @if ($server->info->addresses)
                <i class="fas fa-network-wired"></i>
                @foreach ($server->info->addresses as $address)
                {{ $address }}
                @endforeach
                </div>
                @endif
            </td>
            
            <td class="text-right">
                {!! $server->status()->badge() !!}
                {{ $server->lastSummary()->time()->diffForHumans() }}
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
