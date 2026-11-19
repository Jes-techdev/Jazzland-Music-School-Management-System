@extends('layouts.dashboard')

@section('title', 'Student Dashboard')
@section('page-heading', 'Student Portal')

@section('content')
    <div class="content-card">
        <h4>Welcome, {{ auth()->user()->name }}!</h4>
        <p class="text-muted">
            Your lessons, bookings and learning progress will appear here.
        </p>
    </div>
@endsection