@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Subnet</div>

        <div class="card-body">
            @if (!$subnet->exists)
            <form method="POST" action="{{ route("subnets.store") }}">
            @else
            <form method="POST"
                  action="{{ route("subnets.update", ["subnet" => $subnet]) }}">
            {{ method_field("PUT") }}
            @endif
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="name">Name</label>

                    <input id="name" type="text"
                           class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                           name="name"
                           value="{{ old('name', $subnet->name) }}" autofocus>

                    @if ($errors->has('name'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="address">Address</label>

                    <input id="address" type="text"
                           class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}"
                           name="address"
                           value="{{ old('address', $subnet->address) }}">

                    @if ($errors->has('address'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('address') }}</strong>
                        </span>
                    @endif
                </div>
                
                <div class="form-group">
                    <label for="mask">Mask</label>

                    <input id="mask" type="text"
                           class="form-control{{ $errors->has('mask') ? ' is-invalid' : '' }}"
                           name="mask"
                           value="{{ old('mask', $subnet->mask) }}">

                    @if ($errors->has('mask'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('mask') }}</strong>
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
