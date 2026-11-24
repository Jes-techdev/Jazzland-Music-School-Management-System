@extends('layouts.dashboard')

@section('content')

<div class="mb-4">
    <a href="{{ route('manager.instructors.show', $instructor) }}"
       class="text-decoration-none">
        <i class="bi bi-arrow-left"></i> Back to Instructor
    </a>

    <h3 class="fw-bold mt-3 mb-1">Instructor Availability</h3>

    <p class="text-muted">
        Manage working hours for
        {{ $instructor->first_name }} {{ $instructor->last_name }}.
    </p>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif

<div class="content-card mb-4">
    <h5 class="fw-bold mb-3">Add Availability Period</h5>

    <form method="POST"
          action="{{ route('manager.instructors.availability.store', $instructor) }}">

        @csrf

        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label">Day of Week</label>

                <select name="day_of_week" class="form-select" required>
                    <option value="">Select day</option>

                    @foreach([
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        0 => 'Sunday'
                    ] as $number => $day)

                        <option value="{{ $number }}"
                            @selected(old('day_of_week') == $number)>
                            {{ $day }}
                        </option>

                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Start Time</label>
                <input type="time" name="start_time"
                       class="form-control"
                       value="{{ old('start_time') }}" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">End Time</label>
                <input type="time" name="end_time"
                       class="form-control"
                       value="{{ old('end_time') }}" required>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">
                    Add Period
                </button>
            </div>

        </div>
    </form>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Weekly Schedule</h5>

    @php
        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            0 => 'Sunday'
        ];
    @endphp

    @foreach($days as $number => $day)
        <div class="border-bottom py-3">
            <div class="fw-semibold mb-2">{{ $day }}</div>

            @php
                $periods = $availabilities->where('day_of_week', $number);
            @endphp

            @forelse($periods as $period)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>
                        {{ \Carbon\Carbon::parse($period->start_time)->format('g:i A') }}
                        –
                        {{ \Carbon\Carbon::parse($period->end_time)->format('g:i A') }}
                    </span>

                    <form method="POST"
                          action="{{ route('manager.instructors.availability.destroy', [$instructor, $period]) }}"
                          onsubmit="return confirm('Remove this availability period?')">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-sm btn-outline-danger">
                            Remove
                        </button>
                    </form>
                </div>
            @empty
                <span class="text-muted small">No availability recorded.</span>
            @endforelse
        </div>
    @endforeach
</div>

@endsection
