<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Music School</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body class="bg-light">

<div class="container min-vh-100 d-flex align-items-center justify-content-center">

    <div class="card shadow-sm border-0" style="width: 100%; max-width: 430px;">

        <div class="card-body p-4">

            <h3 class="text-center mb-2">Music School</h3>

            <p class="text-center text-muted mb-4">
                Sign in to your account
            </p>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">

                @csrf

                <div class="mb-3">
                    <label class="form-label">Email address</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3 form-check">
                    <input
                        type="checkbox"
                        name="remember"
                        class="form-check-input"
                        id="remember"
                    >

                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>

                <button class="btn btn-primary w-100">
                    Sign In
                </button>

            </form>

            <div class="text-center mt-3">
                <a href="{{ route('register') }}">
                    Create an account
                </a>
            </div>

        </div>
    </div>
</div>

</body>
</html>
