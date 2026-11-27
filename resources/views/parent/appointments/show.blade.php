@extends('layouts.public')

@section('content')
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Lesson Request Details</h2>
            <p class="text-muted mb-0">
                Appointment #{{ $appointment->id }}
            </p>
        </div>

        <a href="{{ route('parent.appointments.index') }}"
           class="btn btn-outline-secondary">
            Back to My Requests
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

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
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
                    <strong>Lesson Type</strong>
                    <div>{{ ucfirst($appointment->appointment_type) }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Date</strong>
                    <div>{{ $appointment->start_at->format('d M Y') }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Time</strong>
                    <div>
                        {{ $appointment->start_at->format('h:i A') }}
                        –
                        {{ $appointment->end_at->format('h:i A') }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Status</strong>
                    <div>
                        {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Location</strong>
                    <div>{{ $appointment->location ?? 'To be confirmed' }}</div>
                </div>

                <div class="col-12 mb-3">
                    <strong>Instructor(s)</strong>

                    @forelse($appointment->instructors as $instructor)
                        <div>
                            {{ $instructor->first_name }}
                            {{ $instructor->last_name }}
                        </div>
                    @empty
                        <div class="text-muted">
                            An instructor has not yet been assigned.
                        </div>
                    @endforelse
                </div>

                <div class="col-12">
                    <strong>Notes</strong>
                    <div>{{ $appointment->notes ?? 'No additional notes.' }}</div>
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

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Students</h5>

            @foreach($appointment->students as $student)
                <div>
                    {{ $student->first_name }}
                    {{ $student->last_name }}
                </div>
            @endforeach
        </div>
    </div>

    @if($appointment->status === 'pending')
        <div class="card border-warning mb-4">
            <div class="card-body">

                <h5>Cancel Lesson Request</h5>

                <p class="text-muted">
                    You may cancel this request while it is awaiting manager approval.
                </p>

                <form method="POST"
                      action="{{ route('parent.appointments.cancel', $appointment) }}">

                    @csrf

                    <label class="form-label">
                        Reason for cancellation (optional)
                    </label>

                    <textarea name="cancellation_reason"
                              class="form-control mb-3"
                              rows="3"
                              maxlength="1000"></textarea>

                    <button type="submit"
                            class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to cancel this lesson request?')">
                        Cancel My Request
                    </button>
                </form>

            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Request History</h5>

            @forelse($appointment->history as $history)
                <div class="border-bottom py-2">
                    <strong>
                        {{ ucfirst(str_replace('_', ' ', $history->action)) }}
                    </strong>

                    <div class="small text-muted">
                        {{ $history->created_at->format('d M Y, h:i A') }}
                    </div>

                    @if($history->notes)
                        <div>{{ $history->notes }}</div>
                    @endif
                </div>
            @empty
                <p class="text-muted">No history available.</p>
            @endforelse

        </div>
    </div>

</div>
@endsection
