@extends('layouts.public')

@section('title', 'Edit Child')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <h3>Edit Child Information</h3>

                <p class="text-muted">
                    Update {{ $student->first_name }}'s profile.
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

                <form method="POST"
                      action="{{ route('parent.students.update', $student) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text"
                               name="first_name"
                               class="form-control"
                               value="{{ old('first_name', $student->first_name) }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text"
                               name="last_name"
                               class="form-control"
                               value="{{ old('last_name', $student->last_name) }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date"
                               name="date_of_birth"
                               class="form-control"
                               max="{{ now()->toDateString() }}"
                               value="{{ old('date_of_birth', $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('Y-m-d') : '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select gender</option>
                            <option value="male" @selected(old('gender', $student->gender) === 'male')>Male</option>
                            <option value="female" @selected(old('gender', $student->gender) === 'female')>Female</option>
                            <option value="other" @selected(old('gender', $student->gender) === 'other')>Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Your Relationship to the Child</label>
                        <select name="relationship_type" class="form-select" required>
                            @foreach(['mother', 'father', 'guardian', 'grandparent', 'other'] as $item)
                                <option value="{{ $item }}"
                                    @selected(old('relationship_type', $relationship) === $item)>
                                    {{ ucfirst($item) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-primary">Save Changes</button>

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
