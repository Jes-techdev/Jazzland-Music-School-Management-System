@extends('layouts.public')

@section('content')
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>My Lesson Requests</h2>
            <p class="text-muted mb-0">
                View and manage your lesson bookings.
            </p>
        </div>

        <a href="{{ route('parent.appointments.create') }}"
           class="btn btn-primary">
            Request a Lesson
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

    <div class="card">
        <div class="card-body">

            @forelse($appointments as $appointment)
                <div class="border-bottom py-3">

                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div>
                            <h5>{{ $appointment->course->name }}</h5>

                            <p class="mb-1">
                                Student:
                                @foreach($appointment->students as $student)
                                    {{ $student->first_name }}
                                    {{ $student->last_name }}
                                    @if(!$loop->last), @endif
                                @endforeach
                            </p>

                            <p class="mb-1">
                                Lesson type:
                                {{ ucfirst($appointment->appointment_type) }}
                            </p>

                            <p class="mb-1">
                                Date:
                                {{ $appointment->start_at->format('d M Y, h:i A') }}
                            </p>

                            <p class="mb-2">
                                Instructor:
                                @forelse($appointment->instructors as $instructor)
                                    {{ $instructor->first_name }}
                                    {{ $instructor->last_name }}
                                    @if(!$loop->last), @endif
                                @empty
                                    <span class="text-muted">Not yet assigned</span>
                                @endforelse
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

                        <div>
                            <a href="{{ route('parent.appointments.show', $appointment) }}"
                               class="btn btn-outline-primary btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>

                </div>
            @empty
                <div class="text-center py-4">
                    <p class="text-muted">
                        You have not submitted any lesson requests yet.
                    </p>

                    <a href="{{ route('parent.appointments.create') }}"
                       class="btn btn-primary">
                        Request Your First Lesson
                    </a>
                </div>
            @endforelse

            {{ $appointments->links() }}

        </div>
    </div>
</div>
@endsection
