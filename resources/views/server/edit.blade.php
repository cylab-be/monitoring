@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Device</div>

                <div class="card-body">
                    @if (!$server->exists)
                    <form method="POST" action="{{ route("servers.store") }}">
                    @else
                    <form method="POST"
                          action="{{ route("servers.update", ["server" => $server]) }}">
                        {{ method_field("PUT") }}
                    @endif
                        {{ csrf_field() }}

                        @include("organization.partials.select", ["model" => $server])

                        <div class="form-group">
                            <label for="name">Name</label>

                            <input id="name" type="text"
                                   class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                   name="name"
                                   value="{{ old('name', $server->name) }}" required>

                            @if ($errors->has('name'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="description" >Description</label>

                            <textarea id="description"
                                   class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                                   name="description"
                                   placeholder="You can use Markdown here..."
                                   rows="10">{{ old('description', $server->description) }}</textarea>

                            @if ($errors->has('description'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('description') }}</strong>
                                </span>
                            @endif
                        </div>

                        {{-- only show rack configuration for updating servers --}}
                        @if ($server->exists)
                        @include("server.partials.rack")
                        @endif

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check" aria-hidden="true"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
