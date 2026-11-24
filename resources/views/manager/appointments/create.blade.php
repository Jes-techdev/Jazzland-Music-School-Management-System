@extends('layouts.dashboard')

@section('content')

<div class="mb-4">
    <h3>Create Appointment</h3>
    <p class="text-muted">Schedule an individual or group lesson.</p>
</div>

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

<div class="card">
    <div class="card-body">

        <form method="POST" action="{{ route('manager.appointments.store') }}">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Course</label>

                    <select name="course_id" class="form-select" required>
                        <option value="">Select course</option>

                        @foreach($courses as $course)
                            <option value="{{ $course->id }}"
                                @selected(old('course_id') == $course->id)>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Appointment Type</label>

                    <select name="appointment_type" class="form-select" required>
                        <option value="">Select type</option>
                        <option value="individual"
                            @selected(old('appointment_type') === 'individual')>
                            Individual Lesson
                        </option>
                        <option value="group"
                            @selected(old('appointment_type') === 'group')>
                            Group Lesson
                        </option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date and Time</label>

                    <input type="datetime-local"
                           name="start_at"
                           class="form-control"
                           value="{{ old('start_at') }}"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date and Time</label>

                    <input type="datetime-local"
                           name="end_at"
                           class="form-control"
                           value="{{ old('end_at') }}"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Group Capacity</label>

                    <input type="number"
                           name="capacity"
                           class="form-control"
                           min="1"
                           value="{{ old('capacity') }}"
                           placeholder="Leave blank to use course default">
                    <small class="text-muted">
                        Individual lessons automatically have capacity 1.
                    </small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Location</label>

                    <input type="text"
                           name="location"
                           class="form-control"
                           value="{{ old('location') }}"
                           placeholder="e.g. Piano Room 1">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Students</label>

                    <select name="student_ids[]"
                            class="form-select"
                            multiple
                            size="6"
                            required>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}"
                                @selected(in_array($student->id, old('student_ids', [])))>
                                {{ $student->first_name }}
                                {{ $student->last_name }}
                            </option>
                        @endforeach
                    </select>

                    <small class="text-muted">
                        Hold Ctrl (Windows) to select multiple students.
                    </small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Instructors</label>

                    <select name="instructor_ids[]"
                            class="form-select"
                            multiple
                            size="6"
                            required>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}"
                                @selected(in_array($instructor->id, old('instructor_ids', [])))>
                                {{ $instructor->first_name }}
                                {{ $instructor->last_name }}
                            </option>
                        @endforeach
                    </select>

                    <small class="text-muted">
                        Select one or more instructors.
                    </small>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Notes</label>

                    <textarea name="notes"
                              class="form-control"
                              rows="3">{{ old('notes') }}</textarea>
                </div>

            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Create Appointment
                </button>

                <a href="{{ route('manager.appointments.index') }}"
                   class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>

@endsection
