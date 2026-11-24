@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Instructors</h3>
        <p class="text-muted mb-0">Manage teaching staff and their instruments.</p>
    </div>

    <a href="{{ route('manager.instructors.create') }}"
       class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Instructor
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="content-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Instructor</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Instruments</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($instructors as $instructor)
                    <tr>
                        <td class="fw-semibold">
                            {{ $instructor->first_name }}
                            {{ $instructor->last_name }}
                        </td>

                        <td>{{ $instructor->email ?? '—' }}</td>
                        <td>{{ $instructor->phone ?? '—' }}</td>

                        <td>
                            {{ $instructor->instruments->pluck('name')->join(', ') ?: '—' }}
                        </td>

                        <td>
                            <span class="badge {{ $instructor->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($instructor->status) }}
                            </span>
                        </td>

                        <td>
                            <a href="{{ route('manager.instructors.show', $instructor) }}"
                               class="btn btn-sm btn-outline-primary">View</a>

                            <a href="{{ route('manager.instructors.edit', $instructor) }}"
                               class="btn btn-sm btn-outline-secondary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No instructors registered yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $instructors->links() }}
</div>

@endsection
