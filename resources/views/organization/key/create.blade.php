@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-lg-8  col-sm-12">
            <div class="card">
                <div class="card-header">
                    Create key
                </div>

                <div class="card-body">

                    <form class="form" method="POST"
                          action="{{ action('OrganizationKeysController@store', ["organization" => $organization]) }}">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="name">Organization</label>
                            <p class="form-control">{{ $organization->name }}</p>
                        </div>

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name">Name</label>

                            <input id="name" type="string" class="form-control"
                                   name="name" value="{{ old('name') }}" autofocus required>

                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
