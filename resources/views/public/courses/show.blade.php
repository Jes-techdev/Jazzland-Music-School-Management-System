@extends('layouts.public')

@section('title', $course->name)

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        <a href="{{ route('public.home') }}"
           class="text-decoration-none">
            <i class="bi bi-arrow-left"></i> Back to Courses
        </a>

        <h1 class="mt-4">{{ $course->name }}</h1>

        <p class="lead text-muted">
            {{ $course->description ?: 'Discover this music course at our school.' }}
        </p>

        @auth
            @if(auth()->user()->role?->name === 'parent')
                <a href="{{ route('parent.appointments.create', ['course_id' => $course->id]) }}"
                class="btn btn-primary">
                    Request This Course
                </a>
            @endif
        @else
            <a href="{{ route('register') }}" class="btn btn-primary">
                Register to Request a Lesson
            </a>
        @endauth

        <hr>

        <div class="row">
            <div class="col-md-6 mb-3">
                <strong>Lesson type</strong>
                <div>{{ ucfirst($course->lesson_type) }}</div>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Lesson duration</strong>
                <div>{{ $course->duration_minutes }} minutes</div>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Group capacity</strong>
                <div>{{ $course->default_capacity ?? 'To be arranged' }}</div>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Instruments</strong>

                <div>
                    {{ $course->instruments->pluck('name')->join(', ') ?: 'To be arranged' }}
                </div>
            </div>
        </div>

        <div class="alert alert-info mt-3">
            Online lesson requests will be available once parent
            and student account registration is completed.
        </div>

        <a href="{{ route('register') }}" class="btn btn-primary">
            Register an Account
        </a>

    </div>
</div>

@endsection
