@extends('layouts.public')

@section('title', 'Parent Dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Welcome, {{ $guardian->first_name }}</h2>
        <p class="text-muted mb-0">Manage your family and music lessons.</p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('parent.programmes.index') }}"
           class="btn btn-outline-success">
            <i class="bi bi-music-note-list"></i> My Programme Enrolments
        </a>

        <a href="{{ route('parent.profile') }}" class="btn btn-outline-primary">
            <i class="bi bi-person"></i> My Profile
        </a>
    </div>
</div>

<div class="row g-3 mb-4">

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Registered Children</h6>
                <h2>{{ $students->count() }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Account Type</h6>
                <h4>Parent / Guardian</h4>
            </div>
        </div>
    </div>

</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>My Children</h4>

    <a href="{{ route('parent.students.create') }}"
       class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Child
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>
                                {{ $student->first_name }}
                                {{ $student->last_name }}
                            </td>

                            <td>
                                {{ $student->date_of_birth
                                    ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y')
                                    : 'Not provided' }}
                            </td>

                            <td>{{ ucfirst($student->gender ?? 'Not provided') }}</td>

                            <td>
                                <a href="{{ route('parent.students.edit', $student) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                You have not registered any children yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <a href="{{ route('parent.students.index') }}"
           class="btn btn-outline-secondary">
            View All Children
        </a>

    </div>
</div>

@endsection
