@extends('layouts.public')

@section('title', 'My Profile')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <h3>My Parent Profile</h3>
                <p class="text-muted">Update your contact information.</p>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
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

                <form method="POST" action="{{ route('parent.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text"
                               name="first_name"
                               class="form-control"
                               value="{{ old('first_name', $guardian->first_name) }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text"
                               name="last_name"
                               class="form-control"
                               value="{{ old('last_name', $guardian->last_name) }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               value="{{ old('email', $guardian->email) }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel"
                               name="phone"
                               class="form-control"
                               value="{{ old('phone', $guardian->phone) }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address"
                                  class="form-control"
                                  rows="3">{{ old('address', $guardian->address) }}</textarea>
                    </div>

                    <button class="btn btn-primary">Save Changes</button>

                    <a href="{{ route('parent.dashboard') }}"
                       class="btn btn-outline-secondary">
                        Back
                    </a>
                </form>

            </div>
        </div>

    </div>
</div>

@endsection
