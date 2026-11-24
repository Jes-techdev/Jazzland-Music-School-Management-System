@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Enrol Student</h4>
            <p class="text-muted mb-0">{{ $programme->title }}</p>
        </div>

        <a href="{{ route('manager.programmes.enrolments.index', $programme) }}"
            class="btn btn-outline-secondary">
            Back to Enrolments
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST"
                action="{{ route('manager.programmes.enrolments.store', $programme) }}">

                @csrf

                <div class="mb-3">
                    <label class="form-label">Select Student</label>

                    <select name="student_id" class="form-select" required>
                        <option value="">Choose a student</option>

                        @foreach($students as $student)
                            <option value="{{ $student->id }}"
                                @selected(old('student_id') == $student->id)>
                                {{ $student->first_name }}
                                {{ $student->last_name }}
                            </option>
                        @endforeach
                    </select>

                    @error('student_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes (Optional)</label>

                    <textarea name="notes" rows="4"
                        class="form-control">{{ old('notes') }}</textarea>

                    @error('notes')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary">
                    Confirm Enrolment
                </button>

                <a href="{{ route('manager.programmes.enrolments.index', $programme) }}"
                    class="btn btn-outline-secondary">
                    Cancel
                </a>

            </form>

        </div>
    </div>
</div>
@endsection
