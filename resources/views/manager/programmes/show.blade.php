@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>{{ $programme->title }}</h4>

        <div class="d-flex gap-2">
            <a href="{{ route('manager.programmes.edit', $programme) }}"
                class="btn btn-outline-primary">Edit</a>

            <a href="{{ route('manager.programmes.index') }}"
                class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <p>{{ $programme->description ?: 'No description provided.' }}</p>

            <div class="row">
                <div class="col-md-3">
                    <strong>Start Date</strong><br>
                    {{ $programme->start_date->format('d M Y') }}
                </div>

                <div class="col-md-3">
                    <strong>End Date</strong><br>
                    {{ $programme->end_date->format('d M Y') }}
                </div>

                <div class="col-md-3">
                    <strong>Capacity</strong><br>
                    {{ $programme->capacity ?? 'Unlimited' }}
                </div>

                <div class="col-md-3">
                    <strong>Enrolments</strong><br>
                    {{ $programme->enrolments_count }}
                </div>
            </div>

            <hr>

            <strong>Programme Status:</strong>
            {{ ucfirst($programme->status) }}

            <form method="POST"
                action="{{ route('manager.programmes.status', $programme) }}"
                class="mt-3 d-flex gap-2">
                @csrf
                @method('PATCH')

                <select name="status" class="form-select" style="max-width:220px">
                    @foreach(['draft', 'published', 'ongoing', 'completed', 'cancelled'] as $status)
                        <option value="{{ $status }}"
                            @selected($programme->status === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>

                <button class="btn btn-primary">Update Status</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Scheduled Sessions</strong>
            <a href="{{ route('manager.programmes.sessions.create', $programme) }}"
                class="btn btn-sm btn-primary">
                Add Session
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Course</th>
                            <th>Instructor</th>
                            <th>Time</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programme->sessions as $session)
                            <tr>
                                <td>{{ $session->session_date->format('d M Y') }}</td>
                                <td>{{ $session->course->name }}</td>
                                <td>{{ $session->instructor->first_name }}
                                    {{ $session->instructor->last_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                                    –
                                    {{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }}</td>
                                <td>{{ $session->capacity ?? 'Programme default' }}</td>
                                <td>
                                    @if($session->status === 'scheduled')
                                        <span class="badge bg-success">Scheduled</span>
                                    @else
                                        <span class="badge bg-secondary">Cancelled</span>
                                    @endif
                                </td>

                                <td>
                                    @if($session->status === 'scheduled')
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('manager.programmes.sessions.edit', [$programme, $session]) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                Edit
                                            </a>

                                            <form method="POST"
                                                action="{{ route('manager.programmes.sessions.cancel', [$programme, $session]) }}"
                                                onsubmit="return confirm('Are you sure you want to cancel this session?')">
                                                @csrf
                                                @method('PATCH')

                                                <button class="btn btn-sm btn-outline-danger">
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted">No actions</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No sessions scheduled yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
