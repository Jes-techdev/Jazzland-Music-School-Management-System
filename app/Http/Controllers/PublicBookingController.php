<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Programme;

class PublicBookingController extends Controller
{
    public function index()
    {
        $courses = Course::with('instruments')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $programmes = Programme::with([
            'sessions' => function ($query) {
                $query->where('status', 'scheduled')
                    ->orderBy('session_date')
                    ->orderBy('start_time');
            },
        ])
            ->where('status', 'published')
            ->whereDate('end_date', '>=', today())
            ->orderBy('start_date')
            ->get();

        return view('public.home', compact('courses', 'programmes'));
    }

    public function showCourse(Course $course)
    {
        abort_unless($course->is_active, 404);

        $course->load('instruments');

        return view('public.courses.show', compact('course'));
    }

    public function showProgramme(Programme $programme)
    {
        abort_unless(
            $programme->status === 'published'
            && $programme->end_date >= today()->toDateString(),
            404
        );

        $programme->load([
            'sessions' => function ($query) {
                $query->where('status', 'scheduled')
                    ->with(['course', 'instructor'])
                    ->orderBy('session_date')
                    ->orderBy('start_time');
            },
        ]);

        return view('public.programmes.show', compact('programme'));
    }
}
