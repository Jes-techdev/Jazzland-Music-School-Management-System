@extends('layouts.public')

@section('content')
<div class="container py-5">
    <h2>My Programme Enrolments</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mt-3">
        <div class="card-body">
            @forelse($enrolments as $enrolment)
                <div class="border-bottom py-3">
                    <h5>{{ $enrolment->programme->title }}</h5>

                    <p class="mb-1">
                        Student:
                        {{ $enrolment->student->first_name }}
                        {{ $enrolment->student->last_name }}
                    </p>

                    <p class="mb-1">
                        Enrolled:
                        {{ $enrolment->enrolled_at }}
                    </p>

                    <span class="badge
                        @if($enrolment->status === 'confirmed') bg-success
                        @elseif($enrolment->status === 'pending') bg-warning text-dark
                        @elseif($enrolment->status === 'cancelled') bg-danger
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($enrolment->status) }}
                    </span>
                </div>
            @empty
                <p>You have not submitted any programme enrolment requests yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
