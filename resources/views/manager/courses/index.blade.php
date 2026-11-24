@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Courses</h3>
        <p class="text-muted mb-0">Manage courses offered by the school.</p>
    </div>

    <a href="{{ route('manager.courses.create') }}"
       class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Course
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
                    <th>Course</th>
                    <th>Instruments</th>
                    <th>Lesson Type</th>
                    <th>Duration</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($courses as $course)
                    <tr>
                        <td class="fw-semibold">{{ $course->name }}</td>

                        <td>
                            {{ $course->instruments->pluck('name')->join(', ') ?: '—' }}
                        </td>

                        <td>{{ ucfirst($course->lesson_type) }}</td>

                        <td>{{ $course->duration_minutes }} minutes</td>

                        <td>{{ $course->default_capacity ?? '—' }}</td>

                        <td>
                            <span class="badge {{ $course->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $course->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        <td>
                            <a href="{{ route('manager.courses.show', $course) }}"
                               class="btn btn-sm btn-outline-primary">View</a>

                            <a href="{{ route('manager.courses.edit', $course) }}"
                               class="btn btn-sm btn-outline-secondary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No courses have been created yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $courses->links() }}
</div>

@endsection
