<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Music School</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body class="bg-light">

<div class="container min-vh-100 d-flex align-items-center justify-content-center py-4">

    <div class="card shadow-sm border-0" style="width: 100%; max-width: 480px;">

        <div class="card-body p-4">

            <h3 class="text-center">Join Our Music School</h3>

            <p class="text-center text-muted mb-4">
                Create your account
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">

                @csrf

                <div class="mb-3">
                    <label class="form-label">Full name</label>
                    <input type="text" name="name"
                        class="form-control"
                        value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email"
                        class="form-control"
                        value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                        class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm password</label>
                    <input type="password"
                        name="password_confirmation"
                        class="form-control" required>
                </div>

                <button class="btn btn-primary w-100">
                    Create Account
                </button>

            </form>

            <div class="text-center mt-3">
                Already registered?
                <a href="{{ route('login') }}">Sign in</a>
            </div>

        </div>
    </div>
</div>

</body>
</html>
