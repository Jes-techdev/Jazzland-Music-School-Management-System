@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">
            {{ $student->first_name }} {{ $student->last_name }}
        </h3>
        <p class="text-muted mb-0">Student profile</p>
    </div>

    <a href="{{ route('manager.students.edit', $student) }}"
        class="btn btn-primary">Edit Student</a>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Personal Information</h5>

    <p><strong>Date of Birth:</strong> {{ $student->date_of_birth ?? 'Not provided' }}</p>
    <p><strong>Gender:</strong> {{ ucfirst($student->gender ?? 'Not provided') }}</p>
    <p><strong>Status:</strong> {{ ucfirst($student->status) }}</p>

    <hr>

    <h5 class="fw-bold mb-3">Guardians</h5>

    @forelse($student->guardians as $guardian)
        <div class="border rounded p-3 mb-2">
            <strong>{{ $guardian->first_name }} {{ $guardian->last_name }}</strong>
            <div class="text-muted small">{{ $guardian->phone }}</div>
            <div class="small">
                {{ $guardian->pivot->relationship_type ?? 'Relationship not specified' }}
                @if($guardian->pivot->is_primary)
                    · Primary guardian
                @endif
            </div>
        </div>
    @empty
        <p class="text-muted">No guardians linked yet.</p>
    @endforelse
</div>

@endsection