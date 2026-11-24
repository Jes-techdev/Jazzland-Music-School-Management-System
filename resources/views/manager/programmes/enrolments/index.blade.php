@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Programme Enrolments</h4>
            <p class="text-muted mb-0">{{ $programme->title }}</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('manager.programmes.show', $programme) }}"
                class="btn btn-outline-secondary">
                Back to Programme
            </a>

            <a href="{{ route('manager.programmes.enrolments.create', $programme) }}"
                class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Enrol Student
            </a>
        </div>
    </div>

    @php
        $confirmedCount = $programme->enrolments()
            ->where('status', 'confirmed')
            ->count();
    @endphp

    <div class="card mb-4">
        <div class="card-body">
            <strong>Confirmed Enrolments:</strong>
            {{ $confirmedCount }}

            <span class="text-muted mx-2">|</span>

            <strong>Programme Capacity:</strong>
            {{ $programme->capacity ?? 'Unlimited' }}

            @if($programme->capacity !== null)
                <span class="text-muted mx-2">|</span>

                <strong>Remaining Places:</strong>
                {{ max(0, $programme->capacity - $confirmedCount) }}
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Enrolment Date</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($enrolments as $enrolment)
                            <tr>
                                <td>
                                    {{ $enrolment->student->first_name }}
                                    {{ $enrolment->student->last_name }}
                                </td>

                                <td>
                                    {{ $enrolment->enrolled_at
                                        ? $enrolment->enrolled_at->format('d M Y')
                                        : 'Not confirmed' }}
                                </td>

                                <td>
                                    @php
                                        $badge = match($enrolment->status) {
                                            'confirmed' => 'success',
                                            'pending' => 'warning',
                                            'cancelled' => 'danger',
                                            'completed' => 'primary',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $badge }}">
                                        {{ ucfirst($enrolment->status) }}
                                    </span>
                                </td>

                                <td>{{ $enrolment->notes ?: '—' }}</td>

                                <td>
                                    <form method="POST"
                                        action="{{ route('manager.programmes.enrolments.status', [$programme, $enrolment]) }}"
                                        class="d-flex gap-2">

                                        @csrf
                                        @method('PATCH')

                                        <select name="status"
                                            class="form-select form-select-sm"
                                            style="min-width:130px">

                                            @foreach(['pending', 'confirmed', 'cancelled', 'completed'] as $status)
                                                <option value="{{ $status }}"
                                                    @selected($enrolment->status === $status)>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <button class="btn btn-sm btn-outline-primary">
                                            Save
                                        </button>
                                    </form>

                                    @error('status')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No enrolments recorded for this programme.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

            {{ $enrolments->links() }}
        </div>
    </div>
</div>
@endsection
