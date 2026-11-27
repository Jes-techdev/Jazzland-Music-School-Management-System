@extends('layouts.dashboard')

@section('title', 'Record Attendance')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Record Attendance</h2>
        <p class="text-muted mb-0">
            {{ $lesson->course->name ?? 'Lesson' }}
        </p>
    </div>

    <a href="{{ route('instructor.lessons.show', $lesson->id) }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Lesson
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Please correct the following:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Date:</strong>
                <p>{{ $lesson->start_at->format('d M Y') }}</p>
            </div>

            <div class="col-md-4">
                <strong>Time:</strong>
                <p>
                    {{ $lesson->start_at->format('h:i A') }}
                    -
                    {{ $lesson->end_at->format('h:i A') }}
                </p>
            </div>

            <div class="col-md-4">
                <strong>Status:</strong>
                <p>
                    <span class="badge bg-success">
                        {{ ucfirst($lesson->status) }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Student Attendance</h5>
    </div>

    <div class="card-body">

        @if($lesson->students->isEmpty())

            <div class="alert alert-info mb-0">
                No students have been assigned to this lesson.
            </div>

        @else

            <form method="POST"
                  action="{{ route('instructor.lessons.attendance.save', $lesson->id) }}">

                @csrf

                @foreach($lesson->students as $student)

                    <div class="border rounded p-3 mb-3">

                        <h6 class="mb-3">
                            {{ $student->first_name }}
                            {{ $student->last_name }}
                        </h6>

                        <div class="mb-3">
                            <label class="form-label">
                                Attendance Status
                            </label>

                            <select
                                name="attendance[{{ $student->id }}][status]"
                                class="form-select"
                                required>

                                <option value="">Select attendance</option>

                                <option value="present"
                                    {{ old("attendance.{$student->id}.status", $student->pivot->attendance_status) === 'present' ? 'selected' : '' }}>
                                    Present
                                </option>

                                <option value="absent"
                                    {{ old("attendance.{$student->id}.status", $student->pivot->attendance_status) === 'absent' ? 'selected' : '' }}>
                                    Absent
                                </option>

                            </select>
                        </div>

                        <div>
                            <label class="form-label">
                                Attendance Notes (Optional)
                            </label>

                            <textarea
                                name="attendance[{{ $student->id }}][notes]"
                                class="form-control"
                                rows="2"
                                placeholder="Add any relevant attendance notes...">{{ old("attendance.{$student->id}.notes", $student->pivot->attendance_notes) }}</textarea>
                        </div>

                        @if($student->pivot->attendance_marked_at)
                            <small class="text-muted d-block mt-2">
                                Last recorded:
                                {{ \Carbon\Carbon::parse($student->pivot->attendance_marked_at)->format('d M Y, h:i A') }}
                            </small>
                        @endif

                    </div>

                @endforeach

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i>
                        Save Attendance
                    </button>
                </div>

            </form>

        @endif

    </div>
</div>

@endsection
