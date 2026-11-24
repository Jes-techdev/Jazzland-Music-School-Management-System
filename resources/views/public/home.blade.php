@extends('layouts.public')

@section('title', 'Home')

@section('content')

<div class="p-5 mb-5 bg-white rounded-4 shadow-sm">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h1 class="display-5 fw-bold">
                Discover the Joy of Music
            </h1>

            <p class="lead text-muted">
                Learn an instrument, develop your musical abilities
                and enjoy our music programmes for adults and children.
            </p>

            <a href="#courses" class="btn btn-primary btn-lg">
                Explore Our Courses
            </a>
        </div>

        <div class="col-lg-4 text-center mt-4 mt-lg-0">
            <i class="bi bi-music-note-beamed"
               style="font-size: 100px; color: #0d6efd;"></i>
        </div>
    </div>
</div>

<section id="courses" class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Our Courses</h2>
            <p class="text-muted mb-0">
                Explore the lessons available at our school.
            </p>
        </div>
    </div>

    <div class="row g-4">
        @forelse($courses as $course)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $course->name }}</h5>

                        <p class="text-muted">
                            {{ $course->description ?: 'Explore this music course and develop your skills.' }}
                        </p>

                        <p>
                            <strong>Lesson type:</strong>
                            {{ ucfirst($course->lesson_type) }}
                        </p>

                        <p>
                            <strong>Duration:</strong>
                            {{ $course->duration_minutes }} minutes
                        </p>

                        @if($course->instruments->isNotEmpty())
                            <p>
                                <strong>Instruments:</strong>
                                {{ $course->instruments->pluck('name')->join(', ') }}
                            </p>
                        @endif

                        <a href="{{ route('public.courses.show', $course) }}"
                           class="btn btn-outline-primary">
                            View Course
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Course information will be available soon.
                </div>
            </div>
        @endforelse
    </div>
</section>

<section>
    <h2>Vacation Programmes</h2>

    <p class="text-muted mb-4">
        Discover our upcoming vacation music programmes.
    </p>

    <div class="row g-4">
        @forelse($programmes as $programme)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5>{{ $programme->title }}</h5>

                        <p class="text-muted">
                            {{ $programme->description ?: 'Join us for an exciting music programme.' }}
                        </p>

                        <p>
                            <strong>Starts:</strong>
                            {{ \Carbon\Carbon::parse($programme->start_date)->format('d M Y') }}
                        </p>

                        <p>
                            <strong>Ends:</strong>
                            {{ \Carbon\Carbon::parse($programme->end_date)->format('d M Y') }}
                        </p>

                        <a href="{{ route('public.programmes.show', $programme) }}"
                           class="btn btn-outline-primary">
                            View Programme
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    There are currently no published vacation programmes.
                </div>
            </div>
        @endforelse
    </div>
</section>

@endsection
