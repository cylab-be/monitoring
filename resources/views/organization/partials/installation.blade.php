<div class="card">
    <div class="card-header">Custom installation instructions</div>

    <div class="card-body">
        <form method="POST"
              action="{{ action("OrganizationController@updateInstallation", ["organization" => $organization]) }}">
            {{ method_field("PUT") }}
            {{ csrf_field() }}

            <div class="form-group">                
                <p class='text-muted'>These lines will be added to device installation instructions</p>

                <textarea id="instructions" type="text"
                       class="form-control {{ $errors->has('instructions') ? ' is-invalid' : '' }}"
                       rows='8'
                       name="instructions">{{ old('instructions', $organization->properties()->get("instructions", "")) }}</textarea>

                @if ($errors->has('instructions'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('instructions') }}</strong>
                    </span>
                @endif
                
                <div class='text-sm text-muted'>
                    You can use the following variables:
                    <ul>
                        <li><code>%i%</code> : device id</li>
                        <li><code>%n%</code> : device name</li>
                        <li><code>%t%</code> : device token</li>
                    </ul>
                </div>
            </div>

            <div class="form-group mt-2">
                <button type="submit" class="btn btn-primary btn-sm">
                     Save
                </button>
            </div>
        </form>
    </div>
</div>
