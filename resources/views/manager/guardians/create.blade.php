@extends('layouts.dashboard')

@section('content')

<h3 class="fw-bold mb-1">Register Guardian</h3>
<p class="text-muted mb-4">Enter parent or guardian contact details.</p>

<div class="content-card">
    <form method="POST" action="{{ route('manager.guardians.store') }}">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">First Name *</label>
                <input name="first_name" class="form-control"
                       value="{{ old('first_name') }}" required>
                @error('first_name') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Last Name *</label>
                <input name="last_name" class="form-control"
                       value="{{ old('last_name') }}" required>
                @error('last_name') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Phone Number *</label>
                <input name="phone" type="tel" class="form-control"
                       value="{{ old('phone') }}" required>
                @error('phone') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Email Address</label>
                <input name="email" type="email" class="form-control"
                       value="{{ old('email') }}">
                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary">Register Guardian</button>
            <a href="{{ route('manager.guardians.index') }}"
               class="btn btn-light">Cancel</a>
        </div>
    </form>
</div>

@endsection
