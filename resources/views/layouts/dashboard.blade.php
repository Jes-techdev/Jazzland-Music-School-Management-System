<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dashboard') | Music School</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #172554;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding: 25px 15px;
        }

        .sidebar h4 {
            font-size: 20px;
            margin-bottom: 35px;
        }

        .sidebar a {
            display: block;
            color: #cbd5e1;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 7px;
            margin-bottom: 7px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: white;
        }

        .main-content {
            margin-left: 250px;
            padding: 25px;
        }

        .topbar {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
        }

        .stat-card {
            background: white;
            border: none;
            border-radius: 12px;
            padding: 22px;
            height: 100%;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 22px;
        }

        .content-card {
            background: white;
            border: none;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 20px 8px;
            }

            .sidebar h4,
            .sidebar .menu-text {
                display: none;
            }

            .sidebar a {
                text-align: center;
                padding: 12px 5px;
            }

            .main-content {
                margin-left: 70px;
                padding: 15px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

<aside class="sidebar">

    <h4>
        <i class="bi bi-music-note-beamed"></i>
        <span class="menu-text">Music School</span>
    </h4>

    <a href="{{ route('dashboard') }}" class="active">
        <i class="bi bi-grid"></i>
        <span class="menu-text"> Dashboard</span>
    </a>

    <hr class="border-secondary">

    <small class="text-secondary menu-text">MANAGEMENT</small>

    <a href="{{ auth()->user()->role->name === 'manager' ? route('manager.students.index') : '#' }}">
        <i class="bi bi-people"></i> Students
    </a>

    <a href="{{ auth()->user()->role->name === 'manager' ? route('manager.guardians.index') : '#' }}">
        <i class="bi bi-person-hearts"></i> Guardians
    </a>

    <a href="{{ auth()->user()->role->name === 'manager' ? route('manager.instructors.index') : '#' }}">
        <i class="bi bi-person-workspace"></i> Instructors
    </a>

    <a href="{{ auth()->user()->role->name === 'manager' ? route('manager.courses.index') : '#' }}">
        <i class="bi bi-music-note-list"></i> Courses
    </a>

    <a href="{{ auth()->user()->role->name === 'manager' ? route('manager.programmes.index') : '#'}}"
        class="sidebar-link">
        <i class="bi bi-calendar-event"></i>
        <span>Vacation Programmes</span>
    </a>


    <a href="{{ auth()->user()->role->name === 'manager' ? route('manager.appointments.index') : '#'}}"
    class="nav-link">
        <i class="bi bi-calendar-check"></i>
        Appointments
    </a>

    <a href="{{ auth()->user()->role->name === 'manager' ? route('manager.appointments.requests') : '#'}}"
    class="nav-link">
        <i class="bi bi-inbox me-2"></i>
        Booking Requests
    </a>

    <hr class="border-secondary">

    <a href="#" title="Coming soon">
        <i class="bi bi-gear"></i>
        <span class="menu-text"> Settings</span>
    </a>

</aside>


<main class="main-content">

    <header class="topbar d-flex justify-content-between align-items-center">

        <div>
            <h5 class="mb-0">@yield('page-heading', 'Dashboard')</h5>
            <small class="text-muted">Music School Management System</small>
        </div>

        <div class="d-flex align-items-center gap-3">

            <div class="text-end d-none d-sm-block">
                <strong>{{ auth()->user()->name }}</strong>
                <br>
                <small class="text-muted text-capitalize">
                    {{ auth()->user()->role?->name }}
                </small>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </button>
            </form>

        </div>

    </header>

    @yield('content')

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</body>
</html>
