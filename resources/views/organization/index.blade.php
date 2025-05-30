@extends('layouts.app')

@section('title', 'Organizations')

@section('content')
<div class="container">
    <h1>Organizations</h1>
    <p>
        <a href="{{ action('OrganizationController@create') }}" class="btn btn-primary">
             New
        </a>
    </p>

    <table class="table table-striped">
        <tr>
            <th>Name</th>
            <th></th>
        </tr>
        @foreach($organizations as $organization)
        <tr>
            <td>
                <a href="{{ route('servers.index', ['organization' => $organization]) }}"
                   class="text-decoration-none">
                    {{ $organization->name }}
                </a><br>
                <i class="fas fa-desktop"></i> {{ $organization->servers()->count() }} devices
                <i class="fas fa-server"></i> {{ $organization->racks()->count() }} racks
                <i class="fas fa-network-wired"></i> {{ $organization->subnets()->count() }} subnets
                <i class="fas fa-users"></i> {{ $organization->users()->count() }} users
            </td>
            <td class="text-right">
                <a class="btn btn-primary btn-sm"
                   href="{{ route('servers.index', ['organization' => $organization]) }}">
                     Devices
                </a>

                <a class="btn btn-primary btn-sm"
                   href="{{ route('organizations.show', ['organization' => $organization]) }}">
                     Details
                </a>

                <a class="btn btn-primary btn-sm"
                   href="{{ route('organizations.edit', ['organization' => $organization]) }}">
                     Edit
                </a>

                <form method="POST"
                      action="{{ action('OrganizationController@destroy', ['organization' => $organization]) }}"
                      style="display: inline-block">
                    {{ csrf_field() }}
                    {{ method_field("DELETE") }}
                    <button class="btn btn-danger btn-sm">
                         Delete
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection