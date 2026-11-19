@extends('layouts.dashboard')

@section('title', 'Instructor Dashboard')
@section('page-heading', 'My Teaching Dashboard')

@section('content')
    <div class="content-card">
        <h4>Welcome, {{ auth()->user()->name }}!</h4>
        <p class="text-muted">
            Your assigned classes and teaching schedule will appear here.
        </p>
    </div>
@endsection