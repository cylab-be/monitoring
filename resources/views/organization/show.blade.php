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
