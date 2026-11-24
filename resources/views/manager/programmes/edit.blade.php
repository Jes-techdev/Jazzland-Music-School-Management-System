@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Edit Programme</h4>
        <a href="{{ route('manager.programmes.show', $programme) }}"
            class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST"
                action="{{ route('manager.programmes.update', $programme) }}">
                @csrf
                @method('PUT')

                @include('manager.programmes.partials.form')

                <button class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection
