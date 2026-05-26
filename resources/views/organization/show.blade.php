@extends('layouts.app')

@section('content')
<div class="container">

    <h1>{{ $organization->name }}</h1>

    <div class="card">
        <div class="card-header">
            Public dashboard
        </div>

        <div class="card-body">
            <a class="btn btn-primary btn-sm"
               href="{{ route("organizations.json", [
                   "organization" => $organization,
                   "token" => $organization->dashboard_token]) }}">
                <i class="fas fa-code"></i> JSON
            </a>

            <a class="btn btn-primary btn-sm"
               href="{{ action("OrganizationController@resetToken", [
                   "organization" => $organization]) }}">
                <i class="fas fa-redo-alt"></i> Reset dashboard token
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Users
        </div>

        <div class="card-body">
            <table class="table table-sm">
                @foreach ($organization->users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td class="text-right">
                        <form action="{{ action("OrganizationUserController@destroy", ["organization" => $organization, "user" => $user]) }}"
                            method="post">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </table>

            <p>
                <a class="btn btn-primary btn-sm"
                   href="{{ action("OrganizationUserController@create", ["organization" => $organization]) }}">
                    Invite user to organization
                </a>
            </p>
        </div>
    </div>
    
    @include('organization.partials.details', ["organization" => $organization])

    <div class="card border-danger">
        <div class="card-header">
            Danger zone
        </div>
        <div class="card-body">
            <form method="POST"
                  action="{{ action('OrganizationController@destroy', ['organization' => $organization]) }}"
                  style="display: inline-block">
                {{ csrf_field() }}
                {{ method_field("DELETE") }}
                <button class="btn btn-danger btn-sm">
                     Destroy
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
