@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Create Vacation Programme</h4>
        <a href="{{ route('manager.programmes.index') }}"
            class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('manager.programmes.store') }}">
                @csrf

                @include('manager.programmes.partials.form')

                <button class="btn btn-primary">Create Programme</button>
            </form>
        </div>
    </div>
</div>
@endsection
