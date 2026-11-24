@extends('layouts.public')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <h2>Enrol Your Child</h2>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h5>{{ $programme->title }}</h5>
                    <p>
                        {{ $programme->description }}
                    </p>

                    <p>
                        <strong>Programme dates:</strong>
                        {{ $programme->start_date }} to {{ $programme->end_date }}
                    </p>

                    @if($students->isEmpty())
                        <div class="alert alert-info">
                            You have no active children registered.
                            Please add a child to your account first.
                        </div>
                        <a href="{{ route('parent.students.create') }}"
                           class="btn btn-primary">
                            Add Child
                        </a>
                    @else
                        <form method="POST"
                              action="{{ route('parent.programmes.enrol.store', $programme) }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Select Child</label>
                                <select name="student_id" class="form-select" required>
                                    <option value="">Choose a child</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}"
                                            @selected(old('student_id') == $student->id)>
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Additional Notes (Optional)</label>
                                <textarea name="notes" class="form-control"
                                          rows="3">{{ old('notes') }}</textarea>
                            </div>

                            <button class="btn btn-primary">
                                Submit Enrolment Request
                            </button>

                            <a href="{{ route('public.programmes.show', $programme) }}"
                               class="btn btn-secondary">
                                Cancel
                            </a>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
