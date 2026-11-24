@extends('layouts.public')

@section('title', 'Add Child')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <h3>Register a Child</h3>
                <p class="text-muted">
                    Enter your child's information below.
                </p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('parent.students.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text"
                               name="first_name"
                               class="form-control"
                               value="{{ old('first_name') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text"
                               name="last_name"
                               class="form-control"
                               value="{{ old('last_name') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date"
                               name="date_of_birth"
                               class="form-control"
                               max="{{ now()->toDateString() }}"
                               value="{{ old('date_of_birth') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select gender</option>
                            <option value="male" @selected(old('gender') === 'male')>Male</option>
                            <option value="female" @selected(old('gender') === 'female')>Female</option>
                            <option value="other" @selected(old('gender') === 'other')>Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Your Relationship to the Child</label>
                        <select name="relationship_type" class="form-select" required>
                            <option value="">Select relationship</option>
                            @foreach(['mother', 'father', 'guardian', 'grandparent', 'other'] as $relationship)
                                <option value="{{ $relationship }}"
                                    @selected(old('relationship_type') === $relationship)>
                                    {{ ucfirst($relationship) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-primary">Register Child</button>

                    <a href="{{ route('parent.students.index') }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection
