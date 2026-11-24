@extends('layouts.dashboard')

@section('content')

<h3 class="fw-bold mb-4">Edit Guardian</h3>

<div class="content-card">
    <form method="POST" action="{{ route('manager.guardians.update', $guardian) }}">
        @csrf
        @method('PUT')

        @include('manager.guardians.partials.form')
    </form>
</div>

@endsection