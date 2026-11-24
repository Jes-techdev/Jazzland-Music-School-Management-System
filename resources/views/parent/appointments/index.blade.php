@extends('layouts.public')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Lesson Requests</h2>

        <a href="{{ route('parent.appointments.create') }}"
           class="btn btn-primary">
            Request a Lesson
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            @forelse($appointments as $appointment)
                <div class="border-bottom py-3">
                    <h5>{{ $appointment->course->name }}</h5>

                    <p class="mb-1">
                        Student:
                        @foreach($appointment->students as $student)
                            {{ $student->first_name }} {{ $student->last_name }}
                        @endforeach
                    </p>

                    <p class="mb-1">
                        Lesson type: {{ ucfirst($appointment->appointment_type) }}
                    </p>

                    <p class="mb-1">
                        Requested date:
                        {{ \Carbon\Carbon::parse($appointment->start_at)->format('d M Y, h:i A') }}
                    </p>

                    <p class="mb-1">
                        Duration:
                        {{ \Carbon\Carbon::parse($appointment->start_at)
                            ->diffInMinutes($appointment->end_at) }} minutes
                    </p>

                    <span class="badge
                        @if($appointment->status === 'confirmed') bg-success
                        @elseif($appointment->status === 'pending') bg-warning text-dark
                        @elseif($appointment->status === 'cancelled') bg-danger
                        @else bg-secondary
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                    </span>
                </div>
            @empty
                <p>You have not submitted any lesson requests yet.</p>
            @endforelse

            {{ $appointments->links() }}
        </div>
    </div>
</div>
@endsection