@extends('layouts.public')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Request a Music Lesson</h2>
            <p class="text-muted">
                Submit your preferred lesson details. The school will review
                your request and confirm the schedule.
            </p>

            @if($students->isEmpty())
                <div class="alert alert-info">
                    You need to register a child before requesting a lesson.
                </div>
                <a href="{{ route('parent.students.create') }}" class="btn btn-primary">
                    Register a Child
                </a>
            @elseif($courses->isEmpty())
                <div class="alert alert-info">
                    There are currently no active courses available.
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="{{ route('parent.appointments.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Select Child</label>
                                <select name="student_id" class="form-select" required>
                                    <option value="">Choose a child</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}"
                                            @selected(old('student_id') == $student->id)>
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Course</label>
                                <select name="course_id" class="form-select" required>
                                    <option value="">Choose a course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}"
                                            @selected(old('course_id', $selectedCourseId) == $course->id)>
                                            {{ $course->name }}
                                            ({{ $course->duration_minutes }} minutes)
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Lesson Type</label>
                                <select name="appointment_type" class="form-select" required>
                                    <option value="">Choose lesson type</option>
                                    <option value="individual"
                                        @selected(old('appointment_type') === 'individual')>
                                        Individual lesson
                                    </option>
                                    <option value="group"
                                        @selected(old('appointment_type') === 'group')>
                                        Group lesson
                                    </option>
                                </select>
                                @error('appointment_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Preferred Date and Start Time</label>
                                <input type="datetime-local"
                                       name="start_at"
                                       class="form-control"
                                       value="{{ old('start_at') }}"
                                       min="{{ now()->format('Y-m-d\TH:i') }}"
                                       required>
                                @error('start_at')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Additional Notes (Optional)</label>
                                <textarea name="notes"
                                          class="form-control"
                                          rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Submit Lesson Request
                            </button>

                            <a href="{{ route('parent.appointments.index') }}"
                               class="btn btn-outline-secondary">
                                My Requests
                            </a>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection