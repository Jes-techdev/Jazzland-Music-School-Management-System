@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Add Programme Session</h4>
            <p class="text-muted mb-0">
                Programme: {{ $programme->title }}
            </p>
        </div>

        <a href="{{ route('manager.programmes.show', $programme) }}"
           class="btn btn-outline-secondary">
            Back to Programme
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST"
                action="{{ route('manager.programmes.sessions.store', $programme) }}">

                @csrf

                <div class="mb-3">
                    <label class="form-label">Course</label>

                    <select name="course_id" class="form-select" required>
                        <option value="">Select Course</option>

                        @foreach($courses as $course)
                            @if(in_array($course->lesson_type, ['group', 'both']))
                                <option value="{{ $course->id }}"
                                    @selected(old('course_id') == $course->id)>
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
                        <option value="">Select Instructor</option>

                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}"
                                @selected(old('instructor_id') == $instructor->id)>
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
                        value="{{ old('session_date') }}"
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
                            value="{{ old('start_time') }}"
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
                            value="{{ old('end_time') }}"
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
                        value="{{ old('capacity') }}">

                    <div class="form-text">
                        Leave blank to use the programme's overall capacity.
                    </div>

                    @error('capacity')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Location</label>

                    <input type="text"
                        name="location"
                        class="form-control"
                        placeholder="e.g. Piano Room 1"
                        value="{{ old('location') }}">
                </div>

                <button type="submit" class="btn btn-primary">
                    Schedule Session
                </button>

                <a href="{{ route('manager.programmes.show', $programme) }}"
                    class="btn btn-outline-secondary">
                    Cancel
                </a>

            </form>

        </div>
    </div>
</div>
@endsection
