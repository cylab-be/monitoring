@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Rack</div>

        <div class="card-body">
            @if (!$rack->exists)
            <form method="POST" action="{{ action("RackController@store", ["organization" => $organization]) }}">
            @else
            <form method="POST"
                  action="{{ action("RackController@update", ["organization" => $organization, "rack" => $rack]) }}">
            {{ method_field("PUT") }}
            @endif
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="name">Name</label>

                    <input id="name" type="text"
                           class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                           name="name"
                           value="{{ old('name', $rack->name) }}" autofocus>

                    @if ($errors->has('name'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="height">Height</label>

                    <input id="height"
                           type="number" min="1" max="50" step="1"
                           class="form-control{{ $errors->has('height') ? ' is-invalid' : '' }}"
                           name="height"
                           value="{{ old('height', $rack->height) }}" autofocus>

                    @if ($errors->has('name'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-check"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
