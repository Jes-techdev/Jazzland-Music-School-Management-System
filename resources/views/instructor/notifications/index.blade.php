@extends('layouts.dashboard')

@section('title', 'My Notifications')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>My Notifications</h2>
        <p class="text-muted mb-0">
            View updates about your lessons and schedule.
        </p>
    </div>

    @if ($unreadCount > 0)
        <form method="POST" action="{{ route('instructor.notifications.read-all') }}">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-check2-all"></i> Mark All as Read
            </button>
        </form>
    @endif
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Unread Notifications</h6>
                <h3 class="mb-0">{{ $unreadCount }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Notification History</h5>
    </div>

    <div class="card-body">

        @forelse ($notifications as $notification)
        <div class="border rounded p-3 mb-3
            {{ $notification->read_at
                ? 'bg-light border-light'
                : 'bg-primary-subtle border-primary' }}">

                <div class="d-flex justify-content-between align-items-start">
                    <div class="me-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h6 class="mb-0">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </h6>

                            @if (!$notification->read_at)
                                <span class="badge bg-primary">New</span>
                            @endif
                        </div>

                        <p class="mb-2">
                            {{ $notification->data['message'] ?? '' }}
                        </p>

                        <small class="text-muted">
                            {{ $notification->created_at->format('d M Y, h:i A') }}
                        </small>

                        <div class="mt-2">
                            @if (!empty($notification->data['url']))
                                <a href="{{ route('instructor.notifications.open', $notification->id) }}"
                                class="text-decoration-none">
                                    <i class="bi bi-arrow-right-circle"></i>
                                    View Details
                                </a>
                            @else
                                <span class="text-muted small">
                                    <i class="bi bi-info-circle"></i>
                                    No additional details available.
                                </span>
                            @endif
                        </div>
                    </div>

                    @if (!$notification->read_at)
                        <form method="POST"
                              action="{{ route('instructor.notifications.read', $notification->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                Mark as Read
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-bell display-4 text-muted"></i>
                <h5 class="mt-3">No Notifications Yet</h5>
                <p class="text-muted mb-0">
                    Your lesson and schedule updates will appear here.
                </p>
            </div>
        @endforelse

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>

    </div>
</div>

@endsection
