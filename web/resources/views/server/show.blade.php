@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $server->name }}</div>

                <div class="card-body">
                    <p>Name: {{ $server->name }}</p>

                    <div>
                        <a class="btn btn-primary"
                           href="{{ action('ServerController@edit', ['Server' => $server]) }}">
                            <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                        </a>

                        <form method="POST"
                              action="{{ action('ServerController@destroy', ['Server' => $server]) }}"
                              style="display: inline-block">
                            {{ csrf_field() }}
                            {{ method_field("DELETE") }}
                            <button class="btn btn-danger">
                                <i class="fa fa-times-circle" aria-hidden="true"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
