@extends('layouts.public')

@section('title', 'Parent Registration')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">

                <h2 class="mb-2">Parent Registration</h2>

                <p class="text-muted mb-4">
                    Create an account to manage your children's music lessons
                    and programme enrolments.
                </p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Please correct the following:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text"
                                   name="first_name"
                                   class="form-control"
                                   value="{{ old('first_name') }}"
                                   autocomplete="given-name"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text"
                                   name="last_name"
                                   class="form-control"
                                   value="{{ old('last_name') }}"
                                   autocomplete="family-name"
                                   required>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="{{ old('email') }}"
                                   autocomplete="email"
                                   required>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel"
                                   name="phone"
                                   class="form-control"
                                   value="{{ old('phone') }}"
                                   autocomplete="tel"
                                   required>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Address (Optional)</label>
                            <textarea name="address"
                                      class="form-control"
                                      rows="2"
                                      autocomplete="street-address">{{ old('address') }}</textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   autocomplete="new-password"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   autocomplete="new-password"
                                   required>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        Create Parent Account
                    </button>

                </form>

                <div class="text-center mt-4">
                    Already registered?
                    <a href="{{ route('login') }}">Log in</a>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
