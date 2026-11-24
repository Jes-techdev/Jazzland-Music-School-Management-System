@extends('layouts.dashboard')

@section('content')

<h3 class="fw-bold mb-4">Edit Student</h3>

<div class="content-card">
    <form method="POST"
        action="{{ route('manager.students.update', $student) }}">
        @csrf
        @method('PUT')

        @include('manager.students.partials.form', ['student' => $student])
    </form>
</div>

@endsection