@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold">
            {{ $instructor->first_name }} {{ $instructor->last_name }}
        </h3>
        <p class="text-muted mb-0">Instructor profile</p>
    </div>

    <a href="{{ route('manager.instructors.edit', $instructor) }}"
       class="btn btn-primary">Edit Instructor</a>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Personal Information</h5>

    <p><strong>Email:</strong> {{ $instructor->email ?? '—' }}</p>
    <p><strong>Phone:</strong> {{ $instructor->phone ?? '—' }}</p>
    <p><strong>Hire Date:</strong> {{ $instructor->hire_date ?? '—' }}</p>
    <p><strong>Status:</strong> {{ ucfirst($instructor->status) }}</p>

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

@endsection