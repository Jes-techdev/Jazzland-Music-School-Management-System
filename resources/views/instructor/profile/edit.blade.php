@extends('layouts.dashboard')

@section('title', 'Edit My Profile')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Edit My Profile</h2>
        <p class="text-muted mb-0">Update your personal and contact information.</p>
    </div>

    <a href="{{ route('instructor.profile') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Profile
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-4">

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please correct the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('instructor.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input
                        type="text"
                        name="first_name"
                        id="first_name"
                        class="form-control"
                        value="{{ old('first_name', $instructor->first_name) }}"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input
                        type="text"
                        name="last_name"
                        id="last_name"
                        class="form-control"
                        value="{{ old('last_name', $instructor->last_name) }}"
                        required
                    >
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    value="{{ old('email', $instructor->email) }}"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input
                    type="text"
                    name="phone"
                    id="phone"
                    class="form-control"
                    value="{{ old('phone', $instructor->phone) }}"
                >
            </div>

            <div class="mb-4">
                <label for="bio" class="form-label">Professional Bio</label>
                <textarea
                    name="bio"
                    id="bio"
                    class="form-control"
                    rows="5"
                    maxlength="3000"
                >{{ old('bio', $instructor->bio) }}</textarea>
                <small class="text-muted">Tell students a little about your musical background and experience.</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save Changes
                </button>

                <a href="{{ route('instructor.profile') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>

    </div>


    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Change Password</h5>
        </div>

        <div class="card-body p-4">

            @if (session('password_success'))
                <div class="alert alert-success">
                    {{ session('password_success') }}
                </div>
            @endif

            @if ($errors->has('current_password') || $errors->has('password'))
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->get('current_password') as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                        @foreach ($errors->get('password') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('instructor.profile.password') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="current_password" class="form-label">
                        Current Password
                    </label>
                    <input
                        type="password"
                        name="current_password"
                        id="current_password"
                        class="form-control"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        New Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">
                        Confirm New Password
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-shield-lock"></i> Update Password
                </button>
            </form>

        </div>
    </div>

</div>

@endsection
