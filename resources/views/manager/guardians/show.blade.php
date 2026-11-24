@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold">{{ $guardian->first_name }} {{ $guardian->last_name }}</h3>
        <p class="text-muted mb-0">Guardian profile</p>
    </div>

    <a href="{{ route('manager.guardians.edit', $guardian) }}"
       class="btn btn-outline-primary">Edit Guardian</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="content-card mb-4">
    <h5>Contact Information</h5>
    <p><strong>Phone:</strong> {{ $guardian->phone }}</p>
    <p><strong>Email:</strong> {{ $guardian->email ?? '—' }}</p>
    <p><strong>Address:</strong> {{ $guardian->address ?? '—' }}</p>
</div>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Linked Students</h5>

        <a href="{{ route('manager.guardians.students.edit', $guardian) }}"
           class="btn btn-primary btn-sm">Manage Students</a>
    </div>

    @forelse($guardian->students as $student)
        <div class="border rounded p-3 mb-2">
            <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
            <div class="small text-muted">
                {{ $student->pivot->relationship_type }}
                @if($student->pivot->is_primary) · Primary student @endif
            </div>
        </div>
    @empty
        <p class="text-muted">No students linked to this guardian yet.</p>
    @endforelse
</div>

@endsection