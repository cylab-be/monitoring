@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $organization->name }}</div>

                <div class="card-body">
                    <p>Name: {{ $organization->name }}</p>

                    <div>
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
        </div>
    </div>
</div>
@endsection
