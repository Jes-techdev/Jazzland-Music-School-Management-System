@extends('layouts.dashboard')

@section('title', 'My Profile')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>My Profile</h2>
        <p class="text-muted mb-0">
            View your personal and professional information.
        </p>
    </div>

    <a href="{{ route('instructor.profile.edit') }}"
       class="btn btn-primary">
        <i class="bi bi-pencil-square"></i>
        Edit Profile
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row g-4">

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-4">

                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                     style="width:90px; height:90px; font-size:32px;">

                    {{ strtoupper(substr($instructor->first_name, 0, 1)) }}
                    {{ strtoupper(substr($instructor->last_name, 0, 1)) }}

                </div>

                <h4>
                    {{ $instructor->first_name }}
                    {{ $instructor->last_name }}
                </h4>

                <p class="text-muted mb-2">Instructor</p>

                <span class="badge {{ $instructor->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                    {{ ucfirst($instructor->status) }}
                </span>

            </div>
        </div>
    </div>

    <div class="col-lg-8">

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Personal Information</h5>
            </div>

            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-4 text-muted">First Name</div>
                    <div class="col-md-8 fw-semibold">
                        {{ $instructor->first_name }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Last Name</div>
                    <div class="col-md-8 fw-semibold">
                        {{ $instructor->last_name }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Email Address</div>
                    <div class="col-md-8 fw-semibold">
                        {{ $instructor->email }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Phone Number</div>
                    <div class="col-md-8 fw-semibold">
                        {{ $instructor->phone ?: 'Not provided' }}
                    </div>
                </div>

            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Professional Information</h5>
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <strong>Biography</strong>

                    <p class="text-muted mt-2 mb-0">
                        {{ $instructor->bio ?: 'No biography has been added.' }}
                    </p>
                </div>

                <hr>

                <div class="mb-3">
                    <strong>Assigned Instruments</strong>

                    <div class="mt-2">
                        @forelse($instructor->instruments as $instrument)
                            <span class="badge bg-primary me-1 mb-1">
                                {{ $instrument->name }}
                            </span>
                        @empty
                            <span class="text-muted">
                                No instruments assigned.
                            </span>
                        @endforelse
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-4 text-muted">
                        Employment Date
                    </div>

                    <div class="col-md-8 fw-semibold">
                        {{ $instructor->hire_date
                            ? \Carbon\Carbon::parse($instructor->hire_date)->format('d M Y')
                            : 'Not provided' }}
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

@endsection
