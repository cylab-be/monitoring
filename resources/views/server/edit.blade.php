@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Server</div>

                <div class="card-body">
                    @if (!$server->exists)
                    <form method="POST" action="{{ action("ServerController@store") }}">
                    @else
                    <form method="POST"
                          action="{{ action("ServerController@update", ["server" => $server]) }}">
                        {{ method_field("PUT") }}
                    @endif
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="organization_id">Organization</label>

                            <select id="organization_id"
                                   class="form-control{{ $errors->has('organization_id') ? ' is-invalid' : '' }}"
                                   name="organization_id"
                                   required autofocus>
                                <option value="{{ $server->organization->id }}">{{ $server->organization->name }}</option>
                                @foreach (Auth::user()->organizations as $organization)
                                <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('organization_id'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('organization_id') }}</strong>
                                </span>
                            @endif
                        </div>

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
                        
                        <div class="form-group">
                            <label for="rack_id">Rack</label>

                            <select id="rack_id"
                                   class="form-control{{ $errors->has('rack_id') ? ' is-invalid' : '' }}"
                                   name="rack_id">
                                
                                <option value="0">--</option>
                                @foreach ($organization->racks as $rack)
                                <option value="{{ $rack->id }}">{{ $rack->name }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('organization_id'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('organization_id') }}</strong>
                                </span>
                            @endif
                        </div>
                        
                        <div class="form-group">
                            <label for="size">Form factor</label>

                            <div class="input-group">
                                <input id="size" 
                                       type="number" min="0" max="48" step="1"
                                       class="form-control{{ $errors->has('size') ? ' is-invalid' : '' }}"
                                       name="size"
                                       value="{{ old('size', $server->size) }}">
                                <div class="input-group-append">
                                    <div class="input-group-text">u</div>
                                </div>
                            </div>

                            @if ($errors->has('size'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('size') }}</strong>
                                </span>
                            @endif
                        </div>
                        
                        <div class="form-group">
                            <label for="position">Position (from bottom)</label>

                            <div class="input-group">
                                <input id="position" 
                                       type="number" min="0" max="48" step="1"
                                       class="form-control{{ $errors->has('position') ? ' is-invalid' : '' }}"
                                       name="position"
                                       value="{{ old('position', $server->position) }}">
                                <div class="input-group-append">
                                    <div class="input-group-text">u</div>
                                </div>
                            </div>

                            @if ($errors->has('position'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('position') }}</strong>
                                </span>
                            @endif
                        </div>

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
