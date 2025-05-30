@extends('layouts.app')

@section('content')
<div class="container">

    <h1>{{ $ip->server->name }}</h1>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route("ips.store") }}">

                {{ csrf_field() }}

                <input type="hidden" name="server_id" id="server_id" value="{{ $ip->server->id }}">

                <div class="form-group">
                    <label for="ip">IP</label>

                    <input id="ip" type="text"
                           class="form-control{{ $errors->has('ip') ? ' is-invalid' : '' }}"
                           name="ip"
                           value="{{ old('ip', $ip->ip) }}"
                           autofocus required>

                    @if ($errors->has('ip'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('ip') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="comment">Comment</label>

                    <input id="comment" type="text"
                           class="form-control{{ $errors->has('comment') ? ' is-invalid' : '' }}"
                           name="comment"
                           value="{{ old('comment', $ip->comment) }}">

                    @if ($errors->has('ip'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('ip') }}</strong>
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
