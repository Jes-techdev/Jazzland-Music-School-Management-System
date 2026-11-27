@extends('layouts.dashboard')

@section('title', 'My Availability')
@section('page-heading', 'My Teaching Availability')

@section('content')

@php
    $days = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];
@endphp

{{-- Alerts --}}
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Add Availability --}}
<div class="content-card mb-4">

    <h5 class="fw-bold mb-3">Add Availability</h5>

    <p class="text-muted">
        Specify the days and times when you are available to teach.
    </p>

    <form method="POST" action="{{ route('instructor.availability.store') }}">
        @csrf

        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label">Day of the Week</label>

                <select name="day_of_week" class="form-select" required>
                    <option value="">Select Day</option>

                    @foreach($days as $number => $day)
                        <option value="{{ $number }}"
                            {{ old('day_of_week') == $number ? 'selected' : '' }}>
                            {{ $day }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Start Time</label>

                <input type="time"
                    name="start_time"
                    class="form-control"
                    value="{{ old('start_time') }}"
                    required>
            </div>

            <div class="col-md-3">
                <label class="form-label">End Time</label>

                <input type="time"
                    name="end_time"
                    class="form-control"
                    value="{{ old('end_time') }}"
                    required>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle"></i> Add
                </button>
            </div>

        </div>
    </form>

</div>

{{-- Existing Availability --}}
<div class="content-card">

    <h5 class="fw-bold mb-3">My Weekly Availability</h5>

    @if($availabilities->isEmpty())

        <div class="text-center py-5">
            <i class="bi bi-calendar-x fs-1 text-muted"></i>

            <h5 class="mt-3">No Availability Added</h5>

            <p class="text-muted">
                Add your available teaching days and times above.
            </p>
        </div>

    @else

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($availabilities as $availability)

                        <tr>

                            <td class="fw-semibold">
                                {{ $days[$availability->day_of_week] }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($availability->start_time)->format('h:i A') }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($availability->end_time)->format('h:i A') }}
                            </td>

                            <td>

                                <form method="POST"
                                    action="{{ route('instructor.availability.destroy', $availability->id) }}"
                                    onsubmit="return confirm('Are you sure you want to remove this availability period?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="btn btn-outline-danger btn-sm">

                                        <i class="bi bi-trash"></i>
                                        Remove

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @endif

</div>

@endsection
