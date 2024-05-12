<div class="form-group">
    <label for="rack_id">Rack</label>

    <select id="rack_id"
           class="form-control{{ $errors->has('rack_id') ? ' is-invalid' : '' }}"
           name="rack_id">

        @if ($server->rack)
        <option value="{{ $server->rack->id }}">{{ $server->rack->name }}</option>
        @endif
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