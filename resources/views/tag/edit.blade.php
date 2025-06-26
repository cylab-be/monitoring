@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Tag</div>

        <div class="card-body">
            @if (!$tag->exists)
            <form method="POST" action="{{ route("tags.store") }}">
            @else
            <form method="POST"
                  action="{{ route("tags.update", ["tag" => $tag]) }}">
            {{ method_field("PUT") }}
            @endif
                {{ csrf_field() }}

                @include("organization.partials.select", ["model" => $tag])

                <div class="form-group">
                    <label for="name">Name</label>

                    <input id="name" type="text"
                           class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                           name="name"
                           value="{{ old('name', $tag->name) }}" autofocus>

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
