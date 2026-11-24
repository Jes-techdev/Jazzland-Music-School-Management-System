@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Vacation Programmes</h4>
        <a href="{{ route('manager.programmes.create') }}"
            class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Create Programme
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Programme</th>
                            <th>Dates</th>
                            <th>Sessions</th>
                            <th>Enrolments</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programmes as $programme)
                            <tr>
                                <td>{{ $programme->title }}</td>
                                <td>
                                    {{ $programme->start_date->format('d M Y') }}
                                    –
                                    {{ $programme->end_date->format('d M Y') }}
                                </td>
                                <td>{{ $programme->sessions_count }}</td>
                                <td>{{ $programme->enrolments_count }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($programme->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('manager.programmes.show', $programme) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        Manage
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No programmes created yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $programmes->links() }}
        </div>
    </div>
</div>
@endsection
