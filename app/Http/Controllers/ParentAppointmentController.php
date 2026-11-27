<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Course;
use App\Models\ProgrammeEnrolment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ParentAppointmentController extends Controller
{
    private function guardian()
    {
        return auth()->user()->guardian()->firstOrFail();
    }

    public function create(Request $request)
    {
        $students = $this->guardian()->students()
            ->where('students.status', 'active')
            ->orderBy('first_name')
            ->get();

        $courses = Course::where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedCourseId = $request->query('course_id');

        return view('parent.appointments.create', compact(
            'students',
            'courses',
            'selectedCourseId'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'appointment_type' => ['required', 'in:individual,group'],
            'start_at' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $guardian = $this->guardian();

        $student = $guardian->students()
            ->where('students.id', $data['student_id'])
            ->where('students.status', 'active')
            ->first();

        abort_unless($student, 403, 'You cannot book lessons for this student.');

        $course = Course::where('is_active', true)
            ->findOrFail($data['course_id']);

        if (
            $data['appointment_type'] === 'individual'
            && !in_array($course->lesson_type, ['individual', 'both'])
        ) {
            abort(422, 'This course does not offer individual lessons.');
        }

        if (
            $data['appointment_type'] === 'group'
            && !in_array($course->lesson_type, ['group', 'both'])
        ) {
            abort(422, 'This course does not offer group lessons.');
        }

        $start = Carbon::parse($data['start_at']);
        $end = $start->copy()->addMinutes($course->duration_minutes);

        if (!$start->isSameDay($end)) {
            abort(422, 'The requested lesson must finish on the same day it starts.');
        }

        // Prevent this student from requesting overlapping lessons.
        $hasAppointmentConflict = Appointment::whereHas(
            'students',
            fn ($query) => $query->where('students.id', $student->id)
        )
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_at', '<', $end)
            ->where('end_at', '>', $start)
            ->exists();

        if ($hasAppointmentConflict) {
            abort(422, 'This student already has a lesson request or booking at that time.');
        }

        // Prevent conflicts with confirmed vacation programme sessions.
        $hasProgrammeConflict = DB::table('programme_enrolments')
            ->join('programme_sessions', 'programme_sessions.programme_id', '=', 'programme_enrolments.programme_id')
            ->where('programme_enrolments.student_id', $student->id)
            ->whereIn('programme_enrolments.status', ['confirmed'])
            ->where('programme_sessions.status', 'scheduled')
            ->whereDate('programme_sessions.session_date', $start->toDateString())
            ->where('programme_sessions.start_time', '<', $end->format('H:i:s'))
            ->where('programme_sessions.end_time', '>', $start->format('H:i:s'))
            ->exists();

        if ($hasProgrammeConflict) {
            abort(422, 'This student has a confirmed programme session at that time.');
        }

        DB::transaction(function () use ($data, $course, $student, $start, $end) {
            $appointment = Appointment::create([
                'course_id' => $course->id,
                'appointment_type' => $data['appointment_type'],
                'start_at' => $start,
                'end_at' => $end,
                'capacity' => $data['appointment_type'] === 'individual'
                    ? 1
                    : $course->default_capacity,
                'status' => 'pending',
                'location' => null,
                'notes' => $data['notes'] ?? null,
                'created_by' => null,
                'requested_by' => auth()->id(),
            ]);

            $appointment->students()->attach($student->id);
        });

        return redirect()->route('parent.appointments.index')
            ->with('success', 'Your lesson request has been submitted for manager approval.');
    }

    public function index()
    {
        $studentIds = $this->guardian()->students()->pluck('students.id');

        $appointments = Appointment::with([
            'course',
            'students' => fn ($query) => $query->whereIn('students.id', $studentIds),
        ])
            ->where('requested_by', auth()->id())
            ->whereHas('students', fn ($query) => $query->whereIn('students.id', $studentIds))
            ->orderByDesc('start_at')
            ->paginate(15);

        return view('parent.appointments.index', compact('appointments'));
    }


    public function show(Appointment $appointment)
    {
        $studentIds = $this->guardian()->students()->pluck('students.id');

        $appointment = Appointment::with([
            'course',
            'students',
            'instructors',
            'history.user',
        ])
            ->where('requested_by', auth()->id())
            ->whereHas('students', function ($query) use ($studentIds) {
                $query->whereIn('students.id', $studentIds);
            })
            ->findOrFail($appointment->id);

        return view('parent.appointments.show', compact('appointment'));
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $studentIds = $this->guardian()->students()->pluck('students.id');

        return DB::transaction(function () use ($appointment, $data, $studentIds) {

            $appointment = Appointment::whereKey($appointment->id)
                ->where('requested_by', auth()->id())
                ->whereHas('students', function ($query) use ($studentIds) {
                    $query->whereIn('students.id', $studentIds);
                })
                ->lockForUpdate()
                ->firstOrFail();

            if ($appointment->status !== 'pending') {
                return back()->with(
                    'error',
                    'Only pending lesson requests can be cancelled by parents.'
                );
            }

            $appointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $data['cancellation_reason'] ?? 'Cancelled by parent.',
            ]);

            $appointment->history()->create([
                'user_id' => auth()->id(),
                'action' => 'cancelled',
                'old_status' => 'pending',
                'new_status' => 'cancelled',
                'notes' => 'Lesson request cancelled by parent.',
            ]);

            return redirect()
                ->route('parent.appointments.index')
                ->with('success', 'Your lesson request has been cancelled.');
        });
    }

}