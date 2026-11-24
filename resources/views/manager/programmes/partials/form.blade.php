<div class="mb-3">
    <label class="form-label">Programme Title</label>
    <input type="text" name="title"
        class="form-control @error('title') is-invalid @enderror"
        value="{{ old('title', $programme->title ?? '') }}"
        required>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" rows="4"
        class="form-control">{{ old('description', $programme->description ?? '') }}</textarea>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Start Date</label>
        <input type="date" name="start_date"
            class="form-control"
            value="{{ old('start_date', isset($programme) ? $programme->start_date->format('Y-m-d') : '') }}"
            required>
        @error('start_date')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">End Date</label>
        <input type="date" name="end_date"
            class="form-control"
            value="{{ old('end_date', isset($programme) ? $programme->end_date->format('Y-m-d') : '') }}"
            required>
        @error('end_date')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Maximum Programme Capacity</label>
    <input type="number" name="capacity"
        class="form-control"
        min="1" max="1000"
        value="{{ old('capacity', $programme->capacity ?? '') }}">
    <div class="form-text">
        Leave blank if there is no overall programme limit.
    </div>
    @error('capacity')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>
