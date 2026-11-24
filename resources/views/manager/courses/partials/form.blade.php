<div class="row g-3">

    <div class="col-12">
        <label class="form-label">Course Name *</label>
        <input name="name" class="form-control"
               value="{{ old('name', $course->name ?? '') }}" required>
        @error('name')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Lesson Type *</label>
        <select name="lesson_type" class="form-select" required>
            @foreach(['individual', 'group', 'both'] as $type)
                <option value="{{ $type }}"
                    @selected(old('lesson_type', $course->lesson_type ?? 'both') === $type)>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Duration (minutes) *</label>
        <input type="number" name="duration_minutes"
               class="form-control" min="15" max="480"
               value="{{ old('duration_minutes', $course->duration_minutes ?? 60) }}"
               required>
        @error('duration_minutes')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Default Group Capacity</label>
        <input type="number" name="default_capacity"
               class="form-control" min="2" max="100"
               value="{{ old('default_capacity', $course->default_capacity ?? '') }}">
        <small class="text-muted">Required for group or mixed courses.</small>
        @error('default_capacity')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select">
            <option value="1"
                @selected((string) old('is_active', isset($course) ? (int) $course->is_active : 1) === '1')>
                Active
            </option>
            <option value="0"
                @selected((string) old('is_active', isset($course) ? (int) $course->is_active : 1) === '0')>
                Inactive
            </option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $course->description ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Instruments Covered</label>

        <div class="row">
            @foreach($instruments as $instrument)
                <div class="col-md-4 col-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="instruments[]"
                               value="{{ $instrument->id }}"
                               id="instrument{{ $instrument->id }}"
                               @checked(in_array(
                                   $instrument->id,
                                   old('instruments', isset($course)
                                       ? $course->instruments->pluck('id')->toArray()
                                       : [])
                               ))>

                        <label class="form-check-label"
                               for="instrument{{ $instrument->id }}">
                            {{ $instrument->name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

<div class="mt-4">
    <button class="btn btn-primary">Save Course</button>
    <a href="{{ route('manager.courses.index') }}" class="btn btn-light">Cancel</a>
</div>
