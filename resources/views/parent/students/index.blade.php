@extends('layouts.public')

@section('title', 'My Children')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>My Children</h2>
        <p class="text-muted mb-0">Manage children's profiles.</p>
    </div>

    <a href="{{ route('parent.students.create') }}"
       class="btn btn-primary">
        Add Child
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Relationship</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>

                            <td>
                                {{ $student->date_of_birth
                                    ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y')
                                    : 'Not provided' }}
                            </td>

                            <td>{{ ucfirst($student->gender ?? 'Not provided') }}</td>

                            <td>{{ ucfirst($student->pivot->relationship_type ?? 'Not specified') }}</td>

                            <td>
                                <a href="{{ route('parent.students.edit', $student) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No children registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $students->links() }}

    </div>
</div>

@endsection
