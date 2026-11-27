@extends('layouts.dashboard')

@section('title', 'Instructor Dashboard')
@section('page-heading', 'My Teaching Dashboard')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Welcome, {{ $instructor->first_name }}!</h4>
    <p class="text-muted">
        Here's an overview of your teaching activities.
    </p>
</div>

<div class="row g-4">

    {{-- Today's Lessons --}}
    <div class="col-md-6 col-xl-3">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Today's Lessons</p>
                    <h2 class="fw-bold mb-0">{{ $todayLessons }}</h2>
                </div>
                <i class="bi bi-calendar-check fs-1 text-primary"></i>
            </div>
        </div>
    </div>

    {{-- Upcoming Lessons --}}
    <div class="col-md-6 col-xl-3">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Upcoming Lessons</p>
                    <h2 class="fw-bold mb-0">{{ $upcomingLessons }}</h2>
                </div>
                <i class="bi bi-calendar-event fs-1 text-success"></i>
            </div>
        </div>
    </div>

    {{-- Total Students --}}
    <div class="col-md-6 col-xl-3">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">My Students</p>
                    <h2 class="fw-bold mb-0">{{ $totalStudents }}</h2>
                </div>
                <i class="bi bi-people fs-1 text-warning"></i>
            </div>
        </div>
    </div>

    {{-- Completed Lessons --}}
    <div class="col-md-6 col-xl-3">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Completed Lessons</p>
                    <h2 class="fw-bold mb-0">{{ $completedLessons }}</h2>
                </div>
                <i class="bi bi-check-circle fs-1 text-info"></i>
            </div>
        </div>
    </div>

</div>

<div class="content-card mt-4">
    <h5 class="fw-bold mb-2">Your Teaching Overview</h5>
    <p class="text-muted mb-0">
        Use your instructor portal to manage your teaching schedule,
        view assigned students, update your availability and track
        completed lessons.
    </p>
</div>

@endsection
