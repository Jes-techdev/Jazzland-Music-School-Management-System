@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Edit Programme Session</h4>

        <a href="{{ route('manager.programmes.show', $programme) }}"
           class="btn btn-outline-secondary">
            Back to Programme
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST"
                action="{{ route('manager.programmes.sessions.update', [$programme, $session]) }}">

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Course</label>

                    <select name="course_id" class="form-select" required>
                        @foreach($courses as $course)
                            @if(in_array($course->lesson_type, ['group', 'both']))
                                <option value="{{ $course->id }}"
                                    @selected(old('course_id', $session->course_id) == $course->id)>
                                    {{ $course->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>

                    @error('course_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Instructor</label>

                    <select name="instructor_id" class="form-select" required>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}"
                                @selected(old('instructor_id', $session->instructor_id) == $instructor->id)>
                                {{ $instructor->first_name }}
                                {{ $instructor->last_name }}
                            </option>
                        @endforeach
                    </select>

                    @error('instructor_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Session Date</label>

                    <input type="date"
                        name="session_date"
                        class="form-control"
                        min="{{ $programme->start_date->format('Y-m-d') }}"
                        max="{{ $programme->end_date->format('Y-m-d') }}"
                        value="{{ old('session_date', $session->session_date->format('Y-m-d')) }}"
                        required>

                    @error('session_date')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start Time</label>

                        <input type="time"
                            name="start_time"
                            class="form-control"
                            value="{{ old('start_time', substr($session->start_time, 0, 5)) }}"
                            required>

                        @error('start_time')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">End Time</label>

                        <input type="time"
                            name="end_time"
                            class="form-control"
                            value="{{ old('end_time', substr($session->end_time, 0, 5)) }}"
                            required>

                        @error('end_time')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Session Capacity</label>

                    <input type="number"
                        name="capacity"
                        class="form-control"
                        min="1"
                        max="100"
                        value="{{ old('capacity', $session->capacity) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Location</label>

                    <input type="text"
                        name="location"
                        class="form-control"
                        value="{{ old('location', $session->location) }}">
                </div>

                <button class="btn btn-primary">Save Changes</button>

                <a href="{{ route('manager.programmes.show', $programme) }}"
                    class="btn btn-outline-secondary">
                    Cancel
                </a>

            </form>
        </div>
    </div>
</div>
@endsection
