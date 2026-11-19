@extends('layouts.dashboard')

@section('title', 'Parent Dashboard')
@section('page-heading', 'Parent Portal')

@section('content')
    <div class="content-card">
        <h4>Welcome, {{ auth()->user()->name }}!</h4>
        <p class="text-muted">
            Your children's profiles, bookings and lesson schedules will appear here.
        </p>
    </div>
@endsection