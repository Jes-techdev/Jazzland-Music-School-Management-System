@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold">{{ $course->name }}</h3>
        <p class="text-muted mb-0">Course details</p>
    </div>

    <a href="{{ route('manager.courses.edit', $course) }}"
       class="btn btn-primary">Edit Course</a>
</div>

<div class="content-card">
    <p><strong>Description:</strong></p>
    <p>{{ $course->description ?? 'No description provided.' }}</p>

    <hr>

    <p><strong>Lesson Type:</strong> {{ ucfirst($course->lesson_type) }}</p>
    <p><strong>Duration:</strong> {{ $course->duration_minutes }} minutes</p>
    <p><strong>Default Capacity:</strong> {{ $course->default_capacity ?? 'Not specified' }}</p>
    <p><strong>Status:</strong> {{ $course->is_active ? 'Active' : 'Inactive' }}</p>

    <hr>

    <h5 class="fw-bold">Instruments</h5>

    @forelse($course->instruments as $instrument)
        <span class="badge bg-primary me-1">{{ $instrument->name }}</span>
    @empty
        <p class="text-muted">No instruments assigned.</p>
    @endforelse
</div>

@endsection