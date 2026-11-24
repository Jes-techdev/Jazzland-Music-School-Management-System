@extends('layouts.dashboard')

@section('content')

<h3 class="fw-bold mb-1">Manage Students</h3>
<p class="text-muted mb-4">
    Select the students connected to {{ $guardian->first_name }} {{ $guardian->last_name }}.
</p>

<div class="content-card">
    <form method="POST"
          action="{{ route('manager.guardians.students.update', $guardian) }}">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="alert alert-danger">
                Please correct the highlighted errors.
                {{ $errors->first() }}
            </div>
        @endif

        @foreach($students as $student)
            @php
                $existing = $guardian->students->firstWhere('id', $student->id);
                $selected = in_array($student->id, old('students', $guardian->students->pluck('id')->toArray()));
            @endphp

            <div class="border rounded p-3 mb-3">
                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           name="students[]"
                           value="{{ $student->id }}"
                           id="student{{ $student->id }}"
                           @checked($selected)>

                    <label class="form-check-label fw-semibold"
                           for="student{{ $student->id }}">
                        {{ $student->first_name }} {{ $student->last_name }}
                    </label>
                </div>

                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <label class="form-label small">Relationship</label>
                        <select name="relationships[{{ $student->id }}]"
                                class="form-select form-select-sm">
                            @foreach(['mother', 'father', 'guardian', 'grandparent', 'other'] as $relationship)
                                <option value="{{ $relationship }}"
                                    @selected(old("relationships.{$student->id}", $existing?->pivot->relationship_type ?? 'guardian') === $relationship)>
                                    {{ ucfirst($relationship) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small">Primary student</label>
                        <div class="form-check mt-1">
                            <input class="form-check-input"
                                   type="radio"
                                   name="primary_student_id"
                                   value="{{ $student->id }}"
                                   @checked(old('primary_student_id', $guardian->students->firstWhere('pivot.is_primary', true)?->id) == $student->id)>
                            <label class="form-check-label">Set as primary</label>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @error('primary_student_id')
            <div class="text-danger small mb-2">{{ $message }}</div>
        @enderror

        <button class="btn btn-primary">Save Relationships</button>
        <a href="{{ route('manager.guardians.show', $guardian) }}"
           class="btn btn-light">Cancel</a>
    </form>
</div>

@endsection