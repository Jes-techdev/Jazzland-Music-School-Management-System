@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Booking Requests</h3>
        <p class="text-muted mb-0">
            Review appointment requests submitted by parents.
        </p>
    </div>

    <a href="{{ route('manager.appointments.index') }}"
       class="btn btn-outline-secondary">
        All Appointments
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

        @if($appointments->isEmpty())
            <div class="text-center py-5">
                <h5>No booking requests found</h5>
                <p class="text-muted mb-0">
                    Parent-submitted appointment requests will appear here.
                </p>
            </div>
        @else

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Request</th>
                            <th>Course</th>
                            <th>Student(s)</th>
                            <th>Requested Date</th>
                            <th>Instructor(s)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($appointments as $appointment)
                            <tr>
                                <td>#{{ $appointment->id }}</td>

                                <td>
                                    {{ $appointment->course->name ?? 'N/A' }}
                                </td>

                                <td>
                                    @foreach($appointment->students as $student)
                                        <div>
                                            {{ $student->first_name }}
                                            {{ $student->last_name }}
                                        </div>
                                    @endforeach
                                </td>

                                <td>
                                    {{ $appointment->start_at->format('d M Y') }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $appointment->start_at->format('h:i A') }}
                                        -
                                        {{ $appointment->end_at->format('h:i A') }}
                                    </small>
                                </td>

                                <td>
                                    @forelse($appointment->instructors as $instructor)
                                        <div>
                                            {{ $instructor->first_name }}
                                            {{ $instructor->last_name }}
                                        </div>
                                    @empty
                                        <span class="text-muted">
                                            Not assigned
                                        </span>
                                    @endforelse
                                </td>

                                <td>
                                    @if($appointment->status === 'pending')
                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>
                                    @elseif($appointment->status === 'confirmed')
                                        <span class="badge bg-success">
                                            Confirmed
                                        </span>
                                    @elseif($appointment->status === 'cancelled')
                                        <span class="badge bg-danger">
                                            Cancelled
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('manager.appointments.show', $appointment) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>

                                    @if(in_array($appointment->status, ['pending', 'confirmed']))
                                        <a href="{{ route('manager.appointments.edit', $appointment) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            Edit
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <div class="mt-3">
                {{ $appointments->links() }}
            </div>

        @endif

    </div>
</div>

@endsection