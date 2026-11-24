@extends('layouts.public')

@section('title', $programme->title)

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        <a href="{{ route('public.home') }}"
           class="text-decoration-none">
            <i class="bi bi-arrow-left"></i> Back to Programmes
        </a>

        @auth
            @if(auth()->user()->role?->name === 'parent')
                <a href="{{ route('parent.programmes.enrol', $programme) }}"
                class="btn btn-primary">
                    Enrol Your Child
                </a>
            @endif
        @else
            <a href="{{ route('register') }}" class="btn btn-primary">
                Register to Enrol
            </a>
        @endauth

        <h1 class="mt-4">{{ $programme->title }}</h1>

        <p class="lead text-muted">
            {{ $programme->description ?: 'Join our vacation music programme.' }}
        </p>

        <hr>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <strong>Start date</strong>
                <div>
                    {{ \Carbon\Carbon::parse($programme->start_date)->format('d M Y') }}
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <strong>End date</strong>
                <div>
                    {{ \Carbon\Carbon::parse($programme->end_date)->format('d M Y') }}
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <strong>Programme capacity</strong>
                <div>{{ $programme->capacity }}</div>
            </div>
        </div>

        <h4 class="mb-3">Scheduled Sessions</h4>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Course</th>
                        <th>Instructor</th>
                        <th>Location</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($programme->sessions as $session)
                        <tr>
                            <td>
                                {{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                            </td>

                            <td>{{ $session->course->name }}</td>

                            <td>
                                {{ $session->instructor->first_name }}
                                {{ $session->instructor->last_name }}
                            </td>

                            <td>{{ $session->location ?? 'To be confirmed' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Session details will be announced soon.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mt-4">
            Programme enrolment requests will be available through
            registered parent accounts in a later stage.
        </div>

        <a href="{{ route('register') }}" class="btn btn-primary">
            Register an Account
        </a>

    </div>
</div>

@endsection
