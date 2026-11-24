@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3>Appointments</h3>
        <p class="text-muted mb-0">Manage individual and group lessons.</p>
    </div>

    <a href="{{ route('manager.appointments.create') }}"
       class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Appointment
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Course</th>
                        <th>Type</th>
                        <th>Students</th>
                        <th>Instructors</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td>
                                {{ $appointment->start_at->format('d M Y') }}
                                <br>
                                <small class="text-muted">
                                    {{ $appointment->start_at->format('H:i') }}
                                    -
                                    {{ $appointment->end_at->format('H:i') }}
                                </small>
                            </td>

                            <td>{{ $appointment->course->name }}</td>

                            <td>
                                {{ ucfirst($appointment->appointment_type) }}
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
                                @foreach($appointment->instructors as $instructor)
                                    <div>
                                        {{ $instructor->first_name }}
                                        {{ $instructor->last_name }}
                                    </div>
                                @endforeach
                            </td>

                            <td>
                                <span class="badge
                                    @if($appointment->status === 'confirmed') bg-success
                                    @elseif($appointment->status === 'pending') bg-warning text-dark
                                    @elseif($appointment->status === 'cancelled') bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('manager.appointments.show', $appointment) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No appointments have been created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $appointments->links() }}

    </div>
</div>

@endsection
