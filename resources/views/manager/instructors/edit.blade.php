@extends('layouts.dashboard')

@section('content')

<h3 class="fw-bold mb-1">Edit Instructor</h3>
<p class="text-muted mb-4">Update instructor information.</p>

<div class="content-card">
    <form method="POST"
          action="{{ route('manager.instructors.update', $instructor) }}">

        @csrf
        @method('PUT')

        @include('manager.instructors.partials.form')

    </form>
</div>

@endsection