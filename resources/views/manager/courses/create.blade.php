@extends('layouts.dashboard')

@section('content')

<h3 class="fw-bold mb-1">Create Course</h3>
<p class="text-muted mb-4">Define a new course offered by the school.</p>

<div class="content-card">
    <form method="POST" action="{{ route('manager.courses.store') }}">
        @csrf
        @include('manager.courses.partials.form')
    </form>
</div>

@endsection