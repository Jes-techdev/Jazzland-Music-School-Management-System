<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InstructorDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Find the instructor profile linked to this account.
        $instructor = $user->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        $today = now()->toDateString();

        // Get the instructor's assigned appointments.
        $appointments = $instructor->appointments();

        // Lessons scheduled for today.
        $todayLessons = (clone $appointments)
            ->whereDate('start_at', $today)
            ->where('appointments.status', 'confirmed')
            ->count();

        // Upcoming confirmed lessons.
        $upcomingLessons = (clone $appointments)
            ->where('appointments.start_at', '>', now())
            ->where('appointments.status', 'confirmed')
            ->count();

        // Lessons already completed.
        $completedLessons = (clone $appointments)
            ->where('appointments.status', 'completed')
            ->count();

        // Total distinct students assigned to this instructor.
        $totalStudents = DB::table('appointment_student')
            ->join(
                'appointment_instructor',
                'appointment_student.appointment_id',
                '=',
                'appointment_instructor.appointment_id'
            )
            ->where('appointment_instructor.instructor_id', $instructor->id)
            ->distinct()
            ->count('appointment_student.student_id');

        return view('dashboards.instructor', compact(
            'instructor',
            'todayLessons',
            'upcomingLessons',
            'completedLessons',
            'totalStudents'
        ));
    }
}
