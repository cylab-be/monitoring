<div class="card">
    <div class="card-header">Details</div>

    <div class="card-body">
        @if (!$organization->exists)
        <form method="POST" action="{{ action("OrganizationController@store") }}">
        @else
        <form method="POST"
              action="{{ action("OrganizationController@update", ["organization" => $organization]) }}">
        {{ method_field("PUT") }}
        @endif
            {{ csrf_field() }}

            <div class="form-group">
                <label for="name" >Name</label>

                <input id="name" type="text"
                       class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                       name="name"
                       value="{{ old('name', $organization->name) }}" required>

                @if ($errors->has('name'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group mt-2">
                <button type="submit" class="btn btn-primary btn-sm">
                     Save
                </button>
            </div>
        </form>
    </div>
</div>
