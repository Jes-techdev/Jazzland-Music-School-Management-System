@extends('layouts.dashboard')

@section('content')

<h3 class="fw-bold mb-1">Edit Course</h3>
<p class="text-muted mb-4">Update course information.</p>

<div class="content-card">
    <form method="POST" action="{{ route('manager.courses.update', $course) }}">
        @csrf
        @method('PUT')
        @include('manager.courses.partials.form')
    </form>
</div>

@endsection