@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3>Appointment Details</h3>
        <p class="text-muted mb-0">
            Appointment #{{ $appointment->id }}
        </p>
    </div>

    <a href="{{ route('manager.appointments.index') }}"
       class="btn btn-outline-secondary">
        Back to Appointments
    </a>
</div>


@if(!in_array($appointment->status, ['cancelled', 'completed', 'no_show']))
    <div class="d-flex flex-wrap gap-2 mb-4">
        @if($appointment->status === 'pending')
            <form method="POST"
                action="{{ route('manager.appointments.status', $appointment) }}">
                @csrf
                @method('PATCH')

                <input type="hidden" name="status" value="confirmed">

                <button type="submit"
                        class="btn btn-success"
                        onclick="return confirm('Are you sure you want to confirm this appointment?')">
                    <i class="bi bi-check-circle"></i> Confirm Appointment
                </button>
            </form>
        @endif

        <a href="{{ route('manager.appointments.edit', $appointment) }}"
           class="btn btn-primary">
            <i class="bi bi-pencil"></i> Edit / Reschedule
        </a>

        <form method="POST"
              action="{{ route('manager.appointments.status', $appointment) }}">
            @csrf
            @method('PATCH')

            <input type="hidden" name="status" value="completed">

            <button class="btn btn-success"
                    onclick="return confirm('Mark this appointment as completed?')">
                Mark Completed
            </button>
        </form>

        <form method="POST"
              action="{{ route('manager.appointments.status', $appointment) }}">
            @csrf
            @method('PATCH')

            <input type="hidden" name="status" value="no_show">

            <button class="btn btn-outline-secondary"
                    onclick="return confirm('Mark this appointment as a no-show?')">
                Mark No-Show
            </button>
        </form>

        <button class="btn btn-outline-danger"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#cancelAppointment">
            Cancel Appointment
        </button>

    </div>

    <div class="collapse mb-4" id="cancelAppointment">
        <div class="card card-body border-danger">
            <form method="POST"
                  action="{{ route('manager.appointments.cancel', $appointment) }}">
                @csrf

                <label class="form-label">Reason for cancellation</label>

                <textarea name="cancellation_reason"
                          class="form-control mb-3"
                          rows="3"
                          required></textarea>

                <button class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to cancel this appointment?')">
                    Confirm Cancellation
                </button>
            </form>
        </div>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif


<div class="card mb-4">
    <div class="card-body">

        <h5 class="mb-3">Appointment Information</h5>

        <div class="row">
            <div class="col-md-6 mb-3">
                <strong>Course</strong>
                <div>{{ $appointment->course->name }}</div>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Type</strong>
                <div>{{ ucfirst($appointment->appointment_type) }}</div>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Start</strong>
                <div>{{ $appointment->start_at->format('d M Y, H:i') }}</div>
            </div>

            <div class="col-md-6 mb-3">
                <strong>End</strong>
                <div>{{ $appointment->end_at->format('d M Y, H:i') }}</div>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Capacity</strong>
                <div>{{ $appointment->capacity }}</div>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Status</strong>
                <div>
                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Location</strong>
                <div>{{ $appointment->location ?? 'Not specified' }}</div>
            </div>

            <div class="col-12">
                <strong>Notes</strong>
                <div>{{ $appointment->notes ?? 'No notes' }}</div>
            </div>

            @if($appointment->cancellation_reason)
                <div class="col-12 mt-3">
                    <strong>Cancellation Reason</strong>
                    <div>{{ $appointment->cancellation_reason }}</div>
                </div>
            @endif
        </div>

    </div>
</div>

<div class="row">

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5>Students</h5>

                @forelse($appointment->students as $student)
                    <div class="border-bottom py-2">
                        {{ $student->first_name }}
                        {{ $student->last_name }}
                    </div>
                @empty
                    <p class="text-muted">No students assigned.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5>Instructors</h5>

                @forelse($appointment->instructors as $instructor)
                    <div class="border-bottom py-2">
                        {{ $instructor->first_name }}
                        {{ $instructor->last_name }}
                    </div>
                @empty
                    <p class="text-muted">No instructors assigned.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Appointment History</h5>

        @forelse($appointment->history as $history)
            <div class="border-bottom py-3">
                <strong>{{ ucfirst($history->action) }}</strong>

                <div class="text-muted small">
                    {{ $history->created_at->format('d M Y, H:i') }}
                    —
                    {{ $history->user?->name ?? 'System' }}
                </div>

                @if($history->notes)
                    <div>{{ $history->notes }}</div>
                @endif
            </div>
        @empty
            <p class="text-muted">No history recorded.</p>
        @endforelse
    </div>
</div>

@endsection
