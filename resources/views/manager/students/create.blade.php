@extends('layouts.dashboard')

@section('content')

<h3 class="fw-bold mb-1">Register Student</h3>
<p class="text-muted mb-4">Enter the student's details below.</p>

<div class="content-card">

    <form method="POST" action="{{ route('manager.students.store') }}">
        @csrf

        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label">First Name *</label>
                <input type="text" name="first_name"
                    class="form-control"
                    value="{{ old('first_name') }}" required>
                @error('first_name')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name"
                    class="form-control"
                    value="{{ old('last_name') }}" required>
                @error('last_name')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth"
                    class="form-control"
                    value="{{ old('date_of_birth') }}">
                @error('date_of_birth')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                @error('gender')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                Register Student
            </button>

            <a href="{{ route('manager.students.index') }}"
                class="btn btn-light">
                Cancel
            </a>
        </div>

    </form>

</div>

@endsection
