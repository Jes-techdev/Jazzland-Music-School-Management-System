<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label">First Name *</label>
        <input type="text" name="first_name" class="form-control"
            value="{{ old('first_name', $student->first_name) }}" required>
        @error('first_name')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Last Name *</label>
        <input type="text" name="last_name" class="form-control"
            value="{{ old('last_name', $student->last_name) }}" required>
        @error('last_name')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Date of Birth</label>
        <input type="date" name="date_of_birth" class="form-control"
            value="{{ old('date_of_birth', $student->date_of_birth) }}">
        @error('date_of_birth')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select">
            <option value="">Select gender</option>
            @foreach(['male', 'female', 'other'] as $gender)
                <option value="{{ $gender }}"
                    @selected(old('gender', $student->gender) === $gender)>
                    {{ ucfirst($gender) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach(['active', 'inactive'] as $status)
                <option value="{{ $status }}"
                    @selected(old('status', $student->status) === $status)>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
    </div>

</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="{{ route('manager.students.index') }}" class="btn btn-light">Cancel</a>
</div>