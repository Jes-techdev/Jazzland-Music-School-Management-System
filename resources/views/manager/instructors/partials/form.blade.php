<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label">First Name *</label>
        <input name="first_name" class="form-control"
               value="{{ old('first_name', $instructor->first_name ?? '') }}" required>
        @error('first_name')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Last Name *</label>
        <input name="last_name" class="form-control"
               value="{{ old('last_name', $instructor->last_name ?? '') }}" required>
        @error('last_name')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control"
               value="{{ old('email', $instructor->email ?? '') }}">
        @error('email')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input name="phone" class="form-control"
               value="{{ old('phone', $instructor->phone ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Hire Date</label>
        <input type="date" name="hire_date" class="form-control"
               value="{{ old('hire_date', $instructor->hire_date ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="active"
                @selected(old('status', $instructor->status ?? 'active') === 'active')>
                Active
            </option>
            <option value="inactive"
                @selected(old('status', $instructor->status ?? 'active') === 'inactive')>
                Inactive
            </option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Biography / Notes</label>
        <textarea name="bio" class="form-control" rows="3">{{ old('bio', $instructor->bio ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Instruments Taught</label>

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
                                   old('instruments', isset($instructor)
                                       ? $instructor->instruments->pluck('id')->toArray()
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

        @error('instruments')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">Save Instructor</button>
    <a href="{{ route('manager.instructors.index') }}"
       class="btn btn-light">Cancel</a>
</div>
