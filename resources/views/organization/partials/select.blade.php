<div class="form-group">
    <label for="organization_id">Organization</label>

    <select id="organization_id"
           class="form-control{{ $errors->has('organization_id') ? ' is-invalid' : '' }}"
           name="organization_id"
           required>
        <option value="{{ $model->organization->id }}">{{ $model->organization->name }}</option>
        @foreach (Auth::user()->organizations as $o)
        <option value="{{ $o->id }}">{{ $o->name }}</option>
        @endforeach
    </select>

    @if ($errors->has('organization_id'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('organization_id') }}</strong>
        </span>
    @endif
</div>
