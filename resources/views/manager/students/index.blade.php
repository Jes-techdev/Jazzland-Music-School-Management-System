@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Students</h3>
        <p class="text-muted mb-0">Manage student records.</p>
    </div>

    <a href="{{ route('manager.students.create') }}"
        class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Student
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="content-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Guardians</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td class="fw-semibold">
                            {{ $student->first_name }}
                            {{ $student->last_name }}
                        </td>

                        <td>{{ $student->date_of_birth ?? '—' }}</td>

                        <td>{{ ucfirst($student->gender ?? '—') }}</td>

                        <td>{{ $student->guardians->count() }}</td>

                        <td>
                            <span class="badge {{ $student->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </td>

                        <td>
                            <a href="{{ route('manager.students.show', $student) }}"
                                class="btn btn-sm btn-outline-primary">
                                View
                            </a>

                            <a href="{{ route('manager.students.edit', $student) }}"
                                class="btn btn-sm btn-outline-secondary">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No students registered yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $students->links() }}
    </div>
</div>

@endsection
