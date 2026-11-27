@extends('layouts.dashboard')

@section('title', 'My Teaching Calendar')

@section('content')

<div class="mb-4">
    <h2>My Teaching Calendar</h2>
    <p class="text-muted mb-0">
        View your scheduled teaching commitments.
    </p>
</div>

<div class="card">
    <div class="card-body">

        <div class="d-flex flex-wrap gap-3 mb-4">

            <span><span class="badge" style="background:#f59e0b;"> </span> Pending</span>

            <span><span class="badge" style="background:#2563eb;"> </span> Confirmed</span>

            <span><span class="badge" style="background:#16a34a;"> </span> Completed</span>

            <span><span class="badge" style="background:#dc2626;"> </span> Cancelled</span>

            <span><span class="badge" style="background:#64748b;"> </span> No-show</span>

        </div>

        <div id="instructor-calendar"></div>

    </div>
</div>

@endsection

@push('styles')
<style>
    #instructor-calendar {
        min-height: 650px;
    }

    .fc-event {
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script type="module">
document.addEventListener('DOMContentLoaded', function () {

    const calendarElement = document.getElementById('instructor-calendar');

    const calendar = new FullCalendar.Calendar(calendarElement, {

        plugins: [
            FullCalendar.dayGridPlugin,
            FullCalendar.timeGridPlugin,
            FullCalendar.interactionPlugin
        ],

        initialView: 'dayGridMonth',

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        height: 'auto',

        nowIndicator: true,

        events: {
            url: '{{ route("instructor.calendar.events") }}',
            failure: function () {
                alert('Unable to load your teaching schedule.');
            }
        },

        eventClick: function (info) {
            info.jsEvent.preventDefault();

            if (info.event.url) {
                window.location.href = info.event.url;
            }
        }

    });

    calendar.render();

});
</script>
@endpush
