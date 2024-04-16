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
                <a href="{{ action('OrganizationController@show', ['organization' => $organization]) }}"
                   class="text-decoration-none">
                    {{ $organization->name }}
                </a>
            </td>
            <td class="text-right">
                <a class="btn btn-primary btn-sm"
                   href="{{ action('OrganizationController@show', ['organization' => $organization]) }}">
                     Show
                </a>

                <a class="btn btn-primary btn-sm"
                   href="{{ action('OrganizationController@edit', ['organization' => $organization]) }}">
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