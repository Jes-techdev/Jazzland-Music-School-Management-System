@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold">
            {{ $instructor->first_name }} {{ $instructor->last_name }}
        </h3>
        <p class="text-muted mb-0">Instructor profile</p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('manager.instructors.edit', $instructor) }}"
            class="btn btn-primary">
            Edit Instructor
        </a>

        <a href="{{ route('manager.instructors.availability.index', $instructor) }}"
            class="btn btn-outline-primary">
            <i class="bi bi-calendar-week"></i> Manage Availability
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="content-card mb-4">
    <h5 class="fw-bold mb-3">Personal Information</h5>

    <p><strong>Email:</strong> {{ $instructor->email ?? '—' }}</p>
    <p><strong>Phone:</strong> {{ $instructor->phone ?? '—' }}</p>
    <p><strong>Hire Date:</strong> {{ $instructor->hire_date ?? '—' }}</p>
    <p><strong>Status:</strong> {{ ucfirst($instructor->status) }}</p>

    <form method="POST"
        action="{{ route('manager.instructors.send-password-setup', $instructor) }}">

        @csrf

        <button type="submit" class="btn btn-outline-primary">
            <i class="bi bi-envelope"></i>
            Send Password Setup Link
        </button>

    </form>

    <hr>

    <h5 class="fw-bold mb-3">Biography / Notes</h5>
    <p>{{ $instructor->bio ?? 'No biography provided.' }}</p>

    <hr>

    <h5 class="fw-bold mb-3">Instruments Taught</h5>

    @forelse($instructor->instruments as $instrument)
        <span class="badge bg-primary me-1">
            {{ $instrument->name }}
        </span>
    @empty
        <p class="text-muted">No instruments assigned.</p>
    @endforelse
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Login Account</h5>

    @if($instructor->user_id)

        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i>
            This instructor has a linked login account.
        </div>

        <p>
            <strong>Account Name:</strong>
            {{ $instructor->user->name }}
        </p>

        <p>
            <strong>Login Email:</strong>
            {{ $instructor->user->email }}
        </p>

        <p>
            <strong>Role:</strong>
            {{ ucfirst($instructor->user->role->name) }}
        </p>

    @else

        <div class="alert alert-warning">
            This instructor does not yet have a login account.
        </div>

        @if($instructor->email)

            <p>
                A login account will be created using:
                <strong>{{ $instructor->email }}</strong>
            </p>

            <form method="POST"
                action="{{ route('manager.instructors.create-account', $instructor) }}"
                onsubmit="return confirm('Create a login account for this instructor?');">

                @csrf

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-person-plus"></i>
                    Create Login Account
                </button>

            </form>

        @else

            <p class="text-danger">
                Please add an email address to this instructor's profile before creating a login account.
            </p>

        @endif

    @endif
</div>

@endsection
