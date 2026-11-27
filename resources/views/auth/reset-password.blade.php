<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Set Your Password | Music School</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

<div class="container min-vh-100 d-flex align-items-center justify-content-center">

    <div class="card shadow-sm border-0" style="max-width: 450px; width: 100%;">

        <div class="card-body p-4">

            <h3 class="fw-bold text-center mb-2">
                Set Your Password
            </h3>

            <p class="text-muted text-center mb-4">
                Create a new password for your instructor account.
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

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">

                @csrf

                <input type="hidden"
                    name="token"
                    value="{{ $request->route('token') }}">

                <div class="mb-3">
                    <label for="email" class="form-label">
                        Email Address
                    </label>

                    <input type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email', $request->email) }}"
                        required
                        autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        New Password
                    </label>

                    <input type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        required
                        autocomplete="new-password">
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">
                        Confirm New Password
                    </label>

                    <input type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        required
                        autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Set Password
                </button>

            </form>

        </div>
    </div>

</div>

</body>
</html>
