
@extends('layouts.dashboard')

@section('title', 'Booking Requests')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Booking Requests</h2>
            <p class="text-muted mb-0">
                Review lesson requests submitted by parents.
            </p>
        </div>

        <a href="{{ route('manager.appointments.index') }}"
           class="btn btn-outline-secondary">
            All Appointments
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if($appointments->isEmpty())
                <div class="alert alert-info mb-0">
                    There are currently no pending booking requests.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">

                        <thead>
                            <tr>
                                <th>Parent / Requester</th>
                                <th>Course</th>
                                <th>Student(s)</th>
                                <th>Lesson Type</th>
                                <th>Requested Date & Time</th>
                                <th>Instructor</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $appointment->requester?->name ?? 'Unknown requester' }}
                                        </strong>

                                        <div class="small text-muted">
                                            {{ $appointment->requester?->email ?? 'No email available' }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $appointment->course->name }}
                                    </td>

                                    <td>
                                        @forelse($appointment->students as $student)
                                            <div>
                                                {{ $student->first_name }}
                                                {{ $student->last_name }}
                                            </div>
                                        @empty
                                            <span class="text-muted">
                                                No student assigned
                                            </span>
                                        @endforelse
                                    </td>

                                    <td>
                                        {{ ucfirst($appointment->appointment_type) }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $appointment->start_at->format('d M Y') }}
                                        </strong>

                                        <div class="small text-muted">
                                            {{ $appointment->start_at->format('h:i A') }}
                                            –
                                            {{ $appointment->end_at->format('h:i A') }}
                                        </div>
                                    </td>

                                    <td>
                                        @forelse($appointment->instructors as $instructor)
                                            <div>
                                                {{ $instructor->first_name }}
                                                {{ $instructor->last_name }}
                                            </div>
                                        @empty
                                            <span class="text-warning">
                                                Not assigned
                                            </span>
                                        @endforelse
                                    </td>

                                    <td>
                                        {{ $appointment->created_at->format('d M Y') }}

                                        <div class="small text-muted">
                                            {{ $appointment->created_at->format('h:i A') }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('manager.appointments.show', $appointment) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                {{ $appointments->links() }}
            @endif

        </div>
    </div>

</div>
@endsection
