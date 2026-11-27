@extends('layouts.dashboard')

@section('title', 'Attendance History')

@section('content')

<div class="mb-4">
    <h2>Attendance History</h2>
    <p class="text-muted mb-0">
        Review attendance records for your lessons.
    </p>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Recorded Attendance</h5>
    </div>

    <div class="card-body">

        @if($lessons->isEmpty())

            <div class="alert alert-info mb-0">
                No attendance records are available yet.
            </div>

        @else

            @foreach($lessons as $lesson)

                <div class="border rounded p-3 mb-4">

                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                        <div>
                            <h5 class="mb-1">
                                {{ $lesson->course->name ?? 'Lesson' }}
                            </h5>

                            <small class="text-muted">
                                {{ $lesson->start_at->format('d M Y') }}
                                |
                                {{ $lesson->start_at->format('h:i A') }}
                                -
                                {{ $lesson->end_at->format('h:i A') }}
                            </small>
                        </div>

                        <span class="badge
                            {{ $lesson->status === 'completed' ? 'bg-success' : 'bg-primary' }}">
                            {{ ucfirst($lesson->status) }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Attendance</th>
                                    <th>Notes</th>
                                    <th>Recorded At</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($lesson->students as $student)

                                    @if(in_array($student->pivot->attendance_status, ['present', 'absent']))

                                        <tr>
                                            <td>
                                                {{ $student->first_name }}
                                                {{ $student->last_name }}
                                            </td>

                                            <td>
                                                <span class="badge
                                                    {{ $student->pivot->attendance_status === 'present' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($student->pivot->attendance_status) }}
                                                </span>
                                            </td>

                                            <td>
                                                {{ $student->pivot->attendance_notes ?: '—' }}
                                            </td>

                                            <td>
                                                {{ $student->pivot->attendance_marked_at
                                                    ? \Carbon\Carbon::parse($student->pivot->attendance_marked_at)->format('d M Y, h:i A')
                                                    : '—' }}
                                            </td>
                                        </tr>

                                    @endif

                                @endforeach

                            </tbody>

                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('instructor.lessons.show', $lesson->id) }}"
                           class="btn btn-sm btn-outline-primary">
                            View Lesson Details
                        </a>
                    </div>

                </div>

            @endforeach

            <div class="mt-3">
                {{ $lessons->links() }}
            </div>

        @endif

    </div>
</div>

@endsection
