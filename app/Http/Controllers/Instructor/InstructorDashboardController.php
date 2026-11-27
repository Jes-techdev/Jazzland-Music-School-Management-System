<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

    public function lessons()
    {
        $instructor = auth()->user()->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        $lessons = $instructor->appointments()
            ->with(['course', 'students'])
            ->orderBy('start_at', 'desc')
            ->paginate(15);

        return view('instructor.lessons.index', compact('instructor', 'lessons'));
    }


    public function showLesson($appointmentId)
    {
        $instructor = auth()->user()->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        // Only retrieve appointments assigned to this instructor.
        $lesson = $instructor->appointments()
            ->with(['course', 'students'])
            ->findOrFail($appointmentId);

        return view('instructor.lessons.show', compact(
            'instructor',
            'lesson'
        ));
    }

    public function attendance($appointmentId)
    {
        $instructor = auth()->user()->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        $lesson = $instructor->appointments()
            ->with(['course', 'students'])
            ->findOrFail($appointmentId);

        if (!in_array($lesson->status, ['confirmed', 'completed'])) {
            abort(403, 'Attendance is not available for this lesson.');
        }

        return view('instructor.lessons.attendance', compact(
            'instructor',
            'lesson'
        ));
    }

    public function saveAttendance(Request $request, $appointmentId)
    {
        $instructor = auth()->user()->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        $lesson = $instructor->appointments()
            ->with('students')
            ->findOrFail($appointmentId);

        if ($lesson->status !== 'confirmed') {
            abort(403, 'Attendance can only be updated for confirmed lessons.');
        }

        if (now()->lt($lesson->start_at)) {
            abort(403, 'Attendance can only be recorded once the lesson has started.');
        }

        $validated = $request->validate([
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'in:present,absent'],
            'attendance.*.notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $attendance = $validated['attendance'];

        // Ensure attendance has been submitted for every assigned student.
        foreach ($lesson->students as $student) {
            if (!array_key_exists($student->id, $attendance)) {
                throw ValidationException::withMessages([
                    'attendance' => 'Please record attendance for every student.',
                ]);
            }
        }

        foreach ($lesson->students as $student) {
            $studentAttendance = $attendance[$student->id];

            $lesson->students()->updateExistingPivot($student->id, [
                'attendance_status' => $studentAttendance['status'],
                'attendance_notes' => $studentAttendance['notes'] ?? null,
                'attendance_marked_at' => now(),
                'attendance_marked_by' => auth()->id(),
            ]);
        }

        return redirect()
            ->route('instructor.lessons.show', $lesson->id)
            ->with('success', 'Student attendance has been saved successfully.');
    }

    public function completeLesson($appointmentId)
    {
        $instructor = auth()->user()->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        $lesson = $instructor->appointments()
            ->with('students')
            ->findOrFail($appointmentId);

        if ($lesson->status !== 'confirmed') {
            return back()->with(
                'error',
                'Only confirmed lessons can be marked as completed.'
            );
        }

        if (now()->lt($lesson->end_at)) {
            return back()->with(
                'error',
                'A lesson can only be completed after its scheduled end time.'
            );
        }

        $unrecordedStudents = $lesson->students->filter(function ($student) {
            return !in_array(
                $student->pivot->attendance_status,
                ['present', 'absent'],
                true
            );
        });

        if ($unrecordedStudents->isNotEmpty()) {
            return back()->with(
                'error',
                'Please record attendance for every assigned student before completing this lesson.'
            );
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($lesson) {
            $oldStatus = $lesson->status;

            $lesson->update([
                'status' => 'completed',
            ]);

            $lesson->history()->create([
                'user_id' => auth()->id(),
                'action' => 'lesson_completed',
                'old_status' => $oldStatus,
                'new_status' => 'completed',
                'notes' => 'Lesson marked as completed by the instructor.',
            ]);
        });

        return redirect()
            ->route('instructor.lessons.show', $lesson->id)
            ->with('success', 'Lesson has been marked as completed successfully.');
    }

    public function attendanceHistory()
    {
        $instructor = auth()->user()->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        $lessons = $instructor->appointments()
            ->with(['course', 'students'])
            ->whereHas('students', function ($query) {
                $query->whereIn(
                    'appointment_student.attendance_status',
                    ['present', 'absent']
                );
            })
            ->orderBy('start_at', 'desc')
            ->paginate(15);

        return view('instructor.lessons.attendance-history', compact(
            'instructor',
            'lessons'
        ));
    }

    public function calendarEvents()
    {
        $instructor = auth()->user()->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        $appointments = $instructor->appointments()
            ->with('course')
            ->orderBy('start_at')
            ->get();

            $events = $appointments->map(function ($appointment) {

                $colors = [
                    'pending' => '#f59e0b',
                    'confirmed' => '#2563eb',
                    'completed' => '#16a34a',
                    'cancelled' => '#dc2626',
                    'no_show' => '#64748b',
                ];
            
                $color = $colors[$appointment->status] ?? '#64748b';
            
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->course->name ?? 'Lesson',
                    'start' => $appointment->start_at->toIso8601String(),
                    'end' => $appointment->end_at->toIso8601String(),
            
                    'backgroundColor' => $color,
                    'borderColor' => $color,
            
                    'extendedProps' => [
                        'status' => $appointment->status,
                        'location' => $appointment->location,
                        'appointment_type' => $appointment->appointment_type,
                    ],
            
                    'url' => route(
                        'instructor.lessons.show',
                        $appointment->id
                    ),
                ];
            });

        return response()->json($events);
    }

    public function calendar()
    {
        $instructor = auth()->user()->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        return view('instructor.calendar.index', compact('instructor'));
    }

}
