@extends('layouts.dashboard')

@section('title', 'Lesson Details')
@section('page-heading', 'Lesson Details')

@section('content')

<div class="mb-3">
    <a href="{{ route('instructor.lessons') }}"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to My Lessons
    </a>
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

<div class="content-card">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                {{ $lesson->course->name ?? 'Lesson Details' }}
            </h4>
            <p class="text-muted mb-0">
                Your assigned teaching appointment
            </p>
        </div>

        @php
            $statusClasses = [
                'pending' => 'bg-warning text-dark',
                'confirmed' => 'bg-success',
                'completed' => 'bg-primary',
                'cancelled' => 'bg-danger',
                'no_show' => 'bg-secondary',
            ];
        @endphp

        <span class="badge fs-6 {{ $statusClasses[$lesson->status] ?? 'bg-secondary' }}">
            {{ ucfirst(str_replace('_', ' ', $lesson->status)) }}
        </span>
    </div>

    <hr>

    <h5 class="fw-bold mb-3">Lesson Information</h5>

    <div class="row g-4 mb-4">

        <div class="col-md-6">
            <small class="text-muted">Course</small>
            <p class="fw-semibold">
                {{ $lesson->course->name ?? 'N/A' }}
            </p>
        </div>

        <div class="col-md-6">
            <small class="text-muted">Lesson Type</small>
            <p class="fw-semibold">
                {{ ucfirst($lesson->appointment_type) }}
            </p>
        </div>

        <div class="col-md-6">
            <small class="text-muted">Date</small>
            <p class="fw-semibold">
                {{ $lesson->start_at->format('l, d F Y') }}
            </p>
        </div>

        <div class="col-md-6">
            <small class="text-muted">Time</small>
            <p class="fw-semibold">
                {{ $lesson->start_at->format('h:i A') }}
                -
                {{ $lesson->end_at->format('h:i A') }}
            </p>
        </div>

        @if($lesson->status === 'confirmed')
            <div class="mt-4">
                <a href="{{ route('instructor.lessons.attendance', $lesson->id) }}"
                class="btn btn-primary">
                    <i class="bi bi-clipboard-check"></i>
                    Record Attendance
                </a>
            </div>
        @endif

        @if($lesson->status === 'confirmed' && now()->greaterThanOrEqualTo($lesson->end_at))
            <form method="POST"
                action="{{ route('instructor.lessons.complete', $lesson->id) }}"
                class="mt-3"
                onsubmit="return confirm('Are you sure you want to mark this lesson as completed?')">

                @csrf

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i>
                    Mark Lesson as Completed
                </button>
            </form>
        @endif

        <div class="col-md-6">
            <small class="text-muted">Location</small>
            <p class="fw-semibold">
                {{ $lesson->location ?: 'Not specified' }}
            </p>
        </div>

    </div>

    <hr>

    <h5 class="fw-bold mb-3">Assigned Students</h5>

    @forelse($lesson->students as $student)

        <div class="border rounded p-3 mb-2">
            <div class="d-flex align-items-center gap-3">

                <div class="rounded-circle bg-light p-3">
                    <i class="bi bi-person fs-4"></i>
                </div>

                <div>
                    <strong>
                        {{ $student->first_name }}
                        {{ $student->last_name }}
                    </strong>

                    <div class="text-muted small">
                        Student
                    </div>
                </div>

            </div>
        </div>

    @empty

        <p class="text-muted">No students assigned to this lesson.</p>

    @endforelse

    <hr>

    <h5 class="fw-bold mb-3">Lesson Notes</h5>

    <p class="text-muted">
        {{ $lesson->notes ?: 'No additional notes provided.' }}
    </p>

</div>

@endsection
