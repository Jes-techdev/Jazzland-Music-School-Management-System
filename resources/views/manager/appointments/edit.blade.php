@extends('layouts.dashboard')

@section('content')

<h3 class="mb-1">Edit Appointment</h3>
<p class="text-muted mb-4">
    Update appointment #{{ $appointment->id }}
</p>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">

        <form method="POST"
              action="{{ route('manager.appointments.update', $appointment) }}">
            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select" required>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}"
                                @selected(old('course_id', $appointment->course_id) == $course->id)>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Appointment Type</label>
                    <select name="appointment_type" class="form-select" required>
                        <option value="individual"
                            @selected(old('appointment_type', $appointment->appointment_type) === 'individual')>
                            Individual
                        </option>
                        <option value="group"
                            @selected(old('appointment_type', $appointment->appointment_type) === 'group')>
                            Group
                        </option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Start</label>
                    <input type="datetime-local"
                           name="start_at"
                           class="form-control"
                           value="{{ old('start_at', $appointment->start_at->format('Y-m-d\TH:i')) }}"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">End</label>
                    <input type="datetime-local"
                           name="end_at"
                           class="form-control"
                           value="{{ old('end_at', $appointment->end_at->format('Y-m-d\TH:i')) }}"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Capacity</label>
                    <input type="number"
                           name="capacity"
                           class="form-control"
                           min="1"
                           value="{{ old('capacity', $appointment->capacity) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Location</label>
                    <input type="text"
                           name="location"
                           class="form-control"
                           value="{{ old('location', $appointment->location) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Students</label>
                    <select name="student_ids[]" class="form-select" multiple size="7" required>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}"
                                @selected(in_array($student->id, old('student_ids', $appointment->students->pluck('id')->all())))>
                                {{ $student->first_name }} {{ $student->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Instructors</label>
                    <select name="instructor_ids[]" class="form-select" multiple size="7" required>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}"
                                @selected(in_array($instructor->id, old('instructor_ids', $appointment->instructors->pluck('id')->all())))>
                                {{ $instructor->first_name }} {{ $instructor->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $appointment->notes) }}</textarea>
                </div>

            </div>

            <button class="btn btn-primary">Save Changes</button>

            <a href="{{ route('manager.appointments.show', $appointment) }}"
               class="btn btn-outline-secondary">
                Back
            </a>

        </form>
    </div>
</div>

@endsection
