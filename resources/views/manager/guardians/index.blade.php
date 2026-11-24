@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Guardians</h3>
        <p class="text-muted mb-0">Manage parents and guardian contacts.</p>
    </div>

    <a href="{{ route('manager.guardians.create') }}"
       class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Guardian
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
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Students</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($guardians as $guardian)
                    <tr>
                        <td class="fw-semibold">
                            {{ $guardian->first_name }} {{ $guardian->last_name }}
                        </td>
                        <td>{{ $guardian->phone }}</td>
                        <td>{{ $guardian->email ?? '—' }}</td>
                        <td>{{ $guardian->students_count }}</td>
                        <td>
                            <a href="{{ route('manager.guardians.show', $guardian) }}"
                               class="btn btn-sm btn-outline-primary">View</a>

                            <a href="{{ route('manager.guardians.edit', $guardian) }}"
                               class="btn btn-sm btn-outline-secondary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No guardians registered yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $guardians->links() }}
</div>

@endsection
