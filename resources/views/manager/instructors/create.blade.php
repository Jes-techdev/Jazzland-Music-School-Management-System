@extends('layouts.dashboard')

@section('content')

<h3 class="fw-bold mb-1">Register Instructor</h3>
<p class="text-muted mb-4">Add a new member of the teaching staff.</p>

<div class="content-card">
    <form method="POST" action="{{ route('manager.instructors.store') }}">
        @csrf

        @include('manager.instructors.partials.form')

    </form>
</div>

@endsection