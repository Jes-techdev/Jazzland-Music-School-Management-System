@extends('layouts.dashboard')

@section('title', 'My Lessons')
@section('page-heading', 'My Teaching Schedule')

@section('content')

<div class="content-card">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">My Lessons</h4>
            <p class="text-muted mb-0">
                View your assigned teaching appointments.
            </p>
        </div>

        <span class="badge bg-primary">
            {{ $lessons->total() }} Lessons
        </span>
    </div>

    @if($lessons->isEmpty())

        <div class="text-center py-5">
            <i class="bi bi-calendar-x fs-1 text-muted"></i>
            <h5 class="mt-3">No Lessons Found</h5>
            <p class="text-muted">
                You currently have no assigned lessons.
            </p>
        </div>

    @else

        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Course</th>
                        <th>Students</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($lessons as $lesson)

                        <tr>
                            <td>
                                <strong>
                                    {{ $lesson->course->name ?? 'N/A' }}
                                </strong>
                            </td>

                            <td>
                                @forelse($lesson->students as $student)
                                    <div>
                                        {{ $student->first_name }}
                                        {{ $student->last_name }}
                                    </div>
                                @empty
                                    <span class="text-muted">No students assigned</span>
                                @endforelse
                            </td>

                            <td>
                                {{ $lesson->start_at->format('d M Y') }}
                            </td>

                            <td>
                                {{ $lesson->start_at->format('h:i A') }}
                                -
                                {{ $lesson->end_at->format('h:i A') }}
                            </td>

                            <td>
                                {{ ucfirst($lesson->appointment_type) }}
                            </td>

                            <td>
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-warning text-dark',
                                        'confirmed' => 'bg-success',
                                        'completed' => 'bg-primary',
                                        'cancelled' => 'bg-danger',
                                        'no_show' => 'bg-secondary',
                                    ];
                                @endphp

                                <span class="badge {{ $statusClasses[$lesson->status] ?? 'bg-secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $lesson->status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('instructor.lessons.show', $lesson->id) }}"
                                class="btn btn-outline-primary btn-sm">
                                    View Details
                                </a>
                            </td>
                        </tr>

                    @endforeach
                </tbody>

            </table>
        </div>

        <div class="mt-3">
            {{ $lessons->links() }}
        </div>

    @endif

</div>

@endsection
