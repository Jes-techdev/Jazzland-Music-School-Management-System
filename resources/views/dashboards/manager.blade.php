@extends('layouts.dashboard')

@section('title', 'Manager Dashboard')
@section('page-heading', 'Overview')

@section('content')

<div class="mb-4">
    <h4>Welcome back, {{ auth()->user()->name }}!</h4>
    <p class="text-muted">
        Here's an overview of your music school's activities.
    </p>
</div>

{{-- Statistics --}}

<div class="row g-4 mb-4">

    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-2">Total Students</p>
                    <h3>--</h3>
                    <small class="text-muted">Student records pending</small>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-2">Instructors</p>
                    <h3>--</h3>
                    <small class="text-muted">Instructor records pending</small>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-person-workspace"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-2">Appointments</p>
                    <h3>--</h3>
                    <small class="text-muted">Scheduling module pending</small>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-2">Pending Requests</p>
                    <h3>--</h3>
                    <small class="text-muted">Booking module pending</small>
                </div>
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </div>

</div>


{{-- Main dashboard sections --}}

<div class="row g-4">

    <div class="col-lg-8">

        <div class="content-card mb-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Upcoming Appointments</h5>
                <span class="badge bg-secondary">Coming soon</span>
            </div>

            <div class="text-center py-5">
                <i class="bi bi-calendar3 text-muted"
                    style="font-size: 45px;"></i>

                <h6 class="mt-3">No appointment data yet</h6>

                <p class="text-muted mb-0">
                    Upcoming lessons will appear here once scheduling is implemented.
                </p>
            </div>

        </div>

        <div class="content-card">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Recent Booking Requests</h5>
                <span class="badge bg-secondary">Coming soon</span>
            </div>

            <div class="text-center py-4">
                <i class="bi bi-inbox text-muted"
                    style="font-size: 40px;"></i>

                <p class="text-muted mt-3 mb-0">
                    Booking requests will appear here.
                </p>
            </div>

        </div>

    </div>


    <div class="col-lg-4">

        <div class="content-card mb-4">

            <h5 class="mb-3">Quick Actions</h5>

            <div class="d-grid gap-2">

                <button class="btn btn-outline-primary text-start" disabled>
                    <i class="bi bi-person-plus me-2"></i>
                    Register Student
                </button>

                <button class="btn btn-outline-primary text-start" disabled>
                    <i class="bi bi-person-workspace me-2"></i>
                    Add Instructor
                </button>

                <button class="btn btn-outline-primary text-start" disabled>
                    <i class="bi bi-calendar-plus me-2"></i>
                    Create Appointment
                </button>

                <button class="btn btn-outline-primary text-start" disabled>
                    <i class="bi bi-music-note-list me-2"></i>
                    Create Programme
                </button>

            </div>

        </div>

        <div class="content-card">

            <h5 class="mb-3">System Information</h5>

            <p class="mb-2">
                <i class="bi bi-shield-check text-success me-2"></i>
                Authentication active
            </p>

            <p class="mb-2">
                <i class="bi bi-database-check text-success me-2"></i>
                Database configured
            </p>

            <p class="mb-0">
                <i class="bi bi-people text-primary me-2"></i>
                Role-based access enabled
            </p>

        </div>

    </div>

</div>

@endsection
