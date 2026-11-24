<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Music School') | Music School</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('public.home') }}">
            Music School
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#publicNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="publicNavbar">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('public.home') }}">
                    Home
                </a>

                @guest
                    <a class="nav-link" href="{{ route('login') }}">
                        Login
                    </a>

                    <a class="nav-link" href="{{ route('register') }}">
                        Register
                    </a>
                    
                    @else
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            My Dashboard
                        </a>

                        @if(auth()->user()->role?->name === 'parent')
                            <a class="nav-link" href="{{ route('parent.students.index') }}">
                                My Children
                            </a>

                            <a class="nav-link" href="{{ route('parent.profile') }}">
                                My Profile
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm ms-2">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    @endguest

            </div>
        </div>
    </div>
</nav>

<main class="container py-5">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @yield('content')

</main>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p class="mb-0">
            &copy; {{ date('Y') }} Music School. All rights reserved.
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
