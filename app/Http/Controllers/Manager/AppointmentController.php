<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentHistory;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Notifications\InstructorMessageNotification;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with([
            'course',
            'students',
            'instructors',
        ])
            ->latest('start_at')
            ->paginate(15);

        return view('manager.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $courses = Course::where('is_active', true)
            ->orderBy('name')
            ->get();

        $instructors = Instructor::where('status', 'active')
            ->orderBy('first_name')
            ->get();

        $students = Student::where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('manager.appointments.create', compact(
            'courses',
            'instructors',
            'students'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'appointment_type' => ['required', 'in:individual,group'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['required', 'distinct', 'exists:students,id'],
            'instructor_ids' => ['required', 'array', 'min:1'],
            'instructor_ids.*' => ['required', 'distinct', 'exists:instructors,id'],
        ]);

        $start = Carbon::parse($data['start_at']);
        $end = Carbon::parse($data['end_at']);

        if (!$start->isSameDay($end)) {
            throw ValidationException::withMessages([
                'end_at' => 'An appointment must start and finish on the same day.',
            ]);
        }

        if ($end->lessThanOrEqualTo($start)) {
            throw ValidationException::withMessages([
                'end_at' => 'The appointment end time must be after its start time.',
            ]);
        }

        if (
            $data['appointment_type'] === 'individual'
            && count($data['student_ids']) !== 1
        ) {
            throw ValidationException::withMessages([
                'student_ids' => 'An individual appointment must have exactly one student.',
            ]);
        }

        $course = Course::with('instruments')->findOrFail($data['course_id']);

        if (!$course->is_active) {
            throw ValidationException::withMessages([
                'course_id' => 'This course is currently inactive.',
            ]);
        }

        if (
            ! in_array($course->lesson_type, [
                $data['appointment_type'],
                'both',
            ], true)
        ) {
            throw ValidationException::withMessages([
                'course_id' => 'This course does not support the selected appointment type.',
            ]);
        }

        $capacity = $data['appointment_type'] === 'individual'
            ? 1
            : ($data['capacity'] ?? $course->default_capacity);

        if (! $capacity) {
            throw ValidationException::withMessages([
                'capacity' => 'Please specify a capacity for this group appointment.',
            ]);
        }

        if (count($data['student_ids']) > $capacity) {
            throw ValidationException::withMessages([
                'student_ids' => 'The number of students exceeds the appointment capacity.',
            ]);
        }

        $appointment = DB::transaction(function () use ($data, $course, $start, $end, $capacity) {            // Lock instructor records while checking and creating the booking.
            $instructors = Instructor::whereIn('id', $data['instructor_ids'])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if (
                $instructors->count() !== count($data['instructor_ids'])
                || $instructors->contains(fn ($instructor) => $instructor->status !== 'active')
            ) {
                throw ValidationException::withMessages([
                    'instructor_ids' => 'One or more selected instructors are unavailable.',
                ]);
            }

            $students = Student::whereIn('id', $data['student_ids'])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if (
                $students->count() !== count($data['student_ids'])
                || $students->contains(fn ($student) => $student->status !== 'active')
            ) {
                throw ValidationException::withMessages([
                    'student_ids' => 'One or more selected students are inactive or unavailable.',
                ]);
            }

            // Check instructor qualifications and weekly availability.
            $courseInstrumentIds = $course->instruments->pluck('id');

            foreach ($instructors as $instructor) {
                if ($courseInstrumentIds->isNotEmpty()) {
                    $hasMatchingInstrument = $instructor->instruments()
                        ->whereIn('instruments.id', $courseInstrumentIds)
                        ->exists();

                    if (! $hasMatchingInstrument) {
                        throw ValidationException::withMessages([
                            'instructor_ids' => "{$instructor->first_name} {$instructor->last_name} does not have a required course instrument.",
                        ]);
                    }
                }

                $hasAvailability = $instructor->availabilities()
                    ->where('day_of_week', $start->dayOfWeek)
                    ->where('is_available', true)
                    ->where('start_time', '<=', $start->format('H:i:s'))
                    ->where('end_time', '>=', $end->format('H:i:s'))
                    ->exists();

                if (! $hasAvailability) {
                    throw ValidationException::withMessages([
                        'instructor_ids' => "{$instructor->first_name} {$instructor->last_name} is not available for the selected time.",
                    ]);
                }

                // Check for overlapping appointments.
                $appointmentConflict = Appointment::whereIn('status', ['pending', 'confirmed'])
                    ->where('start_at', '<', $end)
                    ->where('end_at', '>', $start)
                    ->whereHas('instructors', function ($query) use ($instructor) {
                        $query->where('instructors.id', $instructor->id);
                    })
                    ->exists();

                if ($appointmentConflict) {
                    throw ValidationException::withMessages([
                        'instructor_ids' => "{$instructor->first_name} {$instructor->last_name} already has an appointment during this time.",
                    ]);
                }

                // Check for overlapping vacation programme sessions.
                $programmeConflict = DB::table('programme_sessions')
                    ->where('instructor_id', $instructor->id)
                    ->where('session_date', $start->toDateString())
                    ->where('status', 'scheduled')
                    ->where('start_time', '<', $end->format('H:i:s'))
                    ->where('end_time', '>', $start->format('H:i:s'))
                    ->exists();

                if ($programmeConflict) {
                    throw ValidationException::withMessages([
                        'instructor_ids' => "{$instructor->first_name} {$instructor->last_name} has a vacation programme session during this time.",
                    ]);
                }
            }

            // Check whether any selected student already has an appointment.
            foreach ($students as $student) {
                $studentConflict = Appointment::whereIn('status', ['pending', 'confirmed'])
                    ->where('start_at', '<', $end)
                    ->where('end_at', '>', $start)
                    ->whereHas('students', function ($query) use ($student) {
                        $query->where('students.id', $student->id);
                    })
                    ->exists();

                if ($studentConflict) {
                    throw ValidationException::withMessages([
                        'student_ids' => "{$student->first_name} {$student->last_name} already has an appointment during this time.",
                    ]);
                }
            }

            $appointment = Appointment::create([
                'course_id' => $course->id,
                'appointment_type' => $data['appointment_type'],
                'start_at' => $start,
                'end_at' => $end,
                'capacity' => $capacity,
                'status' => 'confirmed',
                'location' => $data['location'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
                'requested_by' => null,
            ]);

            $appointment->students()->sync($data['student_ids']);
            $appointment->instructors()->sync($data['instructor_ids']);

            AppointmentHistory::create([
                'appointment_id' => $appointment->id,
                'user_id' => auth()->id(),
                'action' => 'created',
                'new_status' => 'confirmed',
                'notes' => 'Appointment created by manager.',
            ]);

            return $appointment;
        });

        $this->notifyAssignedInstructors(
            $appointment,
            'New Lesson Assigned',
            'You have been assigned to '
                . $appointment->course->name
                . ' on '
                . $appointment->start_at->format('d M Y')
                . ' at '
                . $appointment->start_at->format('h:i A')
                . '.'
        );

        return redirect()
            ->route('manager.appointments.index')
            ->with('success', 'Appointment created successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load([
            'course',
            'students',
            'instructors',
            'history.user',
        ]);

        return view('manager.appointments.show', compact('appointment'));
    }


    public function edit(Appointment $appointment)
    {
        $appointment->load(['students', 'instructors']);

        if (in_array($appointment->status, ['cancelled', 'completed', 'no_show'], true)) {
            return redirect()
                ->route('manager.appointments.show', $appointment)
                ->with('error', 'This appointment can no longer be edited.');
        }

        $courses = Course::where('is_active', true)->orderBy('name')->get();
        $instructors = Instructor::where('status', 'active')->orderBy('first_name')->get();
        $students = Student::where('status', 'active')->orderBy('first_name')->get();

        return view('manager.appointments.edit', compact(
            'appointment',
            'courses',
            'instructors',
            'students'
        ));
    }

    public function update(Request $request, Appointment $appointment)
    {
        if (in_array($appointment->status, ['cancelled', 'completed', 'no_show'], true)) {
            return back()->with('error', 'This appointment can no longer be edited.');
        }

        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'appointment_type' => ['required', 'in:individual,group'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['required', 'distinct', 'exists:students,id'],
            'instructor_ids' => ['required', 'array', 'min:1'],
            'instructor_ids.*' => ['required', 'distinct', 'exists:instructors,id'],
        ]);

        $start = Carbon::parse($data['start_at']);
        $end = Carbon::parse($data['end_at']);

        if (!$start->isSameDay($end)) {
            throw ValidationException::withMessages([
                'end_at' => 'An appointment must start and finish on the same day.',
            ]);
        }

        if ($data['appointment_type'] === 'individual' && count($data['student_ids']) !== 1) {
            throw ValidationException::withMessages([
                'student_ids' => 'An individual appointment must have exactly one student.',
            ]);
        }

        $course = Course::with('instruments')->findOrFail($data['course_id']);

        if (!$course->is_active) {
            throw ValidationException::withMessages([
                'course_id' => 'This course is currently inactive.',
            ]);
        }

        if (! in_array($course->lesson_type, [$data['appointment_type'], 'both'], true)) {
            throw ValidationException::withMessages([
                'course_id' => 'This course does not support the selected appointment type.',
            ]);
        }

        $capacity = $data['appointment_type'] === 'individual'
            ? 1
            : ($data['capacity'] ?? $course->default_capacity);

        if (! $capacity || count($data['student_ids']) > $capacity) {
            throw ValidationException::withMessages([
                'capacity' => 'The capacity is missing or fewer than the selected students.',
            ]);
        }

        $shouldNotify = false;
        $oldStart = null;
        $oldEnd = null;

        DB::transaction(function () use (
            $data,
            $appointment,
            $course,
            $start,
            $end,
            $capacity,
            &$shouldNotify,
            &$oldStart,
            &$oldEnd
        ) {
            $appointment = Appointment::whereKey($appointment->id)
                ->lockForUpdate()
                ->firstOrFail();
            
            if (in_array($appointment->status, ['cancelled', 'completed', 'no_show'], true)) {
                throw ValidationException::withMessages([
                    'appointment' => 'This appointment can no longer be edited.',
                ]);
            }

            $instructors = Instructor::whereIn('id', $data['instructor_ids'])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $students = Student::whereIn('id', $data['student_ids'])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if (
                $instructors->count() !== count($data['instructor_ids'])
                || $instructors->contains(fn ($i) => $i->status !== 'active')
            ) {
                throw ValidationException::withMessages([
                    'instructor_ids' => 'One or more instructors are inactive.',
                ]);
            }

            if (
                $students->count() !== count($data['student_ids'])
                || $students->contains(fn ($s) => $s->status !== 'active')
            ) {
                throw ValidationException::withMessages([
                    'student_ids' => 'One or more students are inactive.',
                ]);
            }

            $instrumentIds = $course->instruments->pluck('id');

            foreach ($instructors as $instructor) {
                if (
                    $instrumentIds->isNotEmpty()
                    && ! $instructor->instruments()
                        ->whereIn('instruments.id', $instrumentIds)
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'instructor_ids' => "{$instructor->first_name} {$instructor->last_name} lacks a required course instrument.",
                    ]);
                }

                $available = $instructor->availabilities()
                    ->where('day_of_week', $start->dayOfWeek)
                    ->where('is_available', true)
                    ->where('start_time', '<=', $start->format('H:i:s'))
                    ->where('end_time', '>=', $end->format('H:i:s'))
                    ->exists();

                if (! $available) {
                    throw ValidationException::withMessages([
                        'instructor_ids' => "{$instructor->first_name} {$instructor->last_name} is not available at that time.",
                    ]);
                }

                $conflict = Appointment::whereIn('status', ['pending', 'confirmed'])
                    ->where('id', '!=', $appointment->id)
                    ->where('start_at', '<', $end)
                    ->where('end_at', '>', $start)
                    ->whereHas('instructors', fn ($q) => $q->where('instructors.id', $instructor->id))
                    ->exists();

                if ($conflict) {
                    throw ValidationException::withMessages([
                        'instructor_ids' => "{$instructor->first_name} {$instructor->last_name} has another appointment at that time.",
                    ]);
                }

                $programmeConflict = DB::table('programme_sessions')
                    ->where('instructor_id', $instructor->id)
                    ->where('session_date', $start->toDateString())
                    ->where('status', 'scheduled')
                    ->where('start_time', '<', $end->format('H:i:s'))
                    ->where('end_time', '>', $start->format('H:i:s'))
                    ->exists();

                if ($programmeConflict) {
                    throw ValidationException::withMessages([
                        'instructor_ids' => "{$instructor->first_name} {$instructor->last_name} has a programme session at that time.",
                    ]);
                }
            }

            foreach ($students as $student) {
                $conflict = Appointment::whereIn('status', ['pending', 'confirmed'])
                    ->where('id', '!=', $appointment->id)
                    ->where('start_at', '<', $end)
                    ->where('end_at', '>', $start)
                    ->whereHas('students', fn ($q) => $q->where('students.id', $student->id))
                    ->exists();

                if ($conflict) {
                    throw ValidationException::withMessages([
                        'student_ids' => "{$student->first_name} {$student->last_name} has another appointment at that time.",
                    ]);
                }
            }

            $oldStart = $appointment->start_at->format('Y-m-d H:i:s');
            $oldEnd = $appointment->end_at->format('Y-m-d H:i:s');
            $oldCourse = $appointment->course_id;

            // Determine whether the appointment has actually been rescheduled.
            $shouldNotify = (
                $oldStart !== $start->format('Y-m-d H:i:s')
                || $oldEnd !== $end->format('Y-m-d H:i:s')
            );

            $appointment->update([
                'course_id' => $course->id,
                'appointment_type' => $data['appointment_type'],
                'start_at' => $start,
                'end_at' => $end,
                'capacity' => $capacity,
                'location' => $data['location'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $appointment->students()->sync($data['student_ids']);
            $appointment->instructors()->sync($data['instructor_ids']);

            $appointment->history()->create([
                'user_id' => auth()->id(),
                'action' => 'updated',
                'notes' => 'Appointment details updated. Previous time: '
                    . $oldStart . ' to ' . $oldEnd
                    . '. Previous course ID: ' . $oldCourse,
            ]);
        });

        // Send notifications only after the transaction succeeds.
        if ($shouldNotify) {
            $appointment->refresh();
        
            $this->notifyAssignedInstructors(
                $appointment,
                'Lesson Rescheduled',
                'Your lesson for '
                    . $appointment->course->name
                    . ' has been rescheduled. Previous schedule: '
                    . \Carbon\Carbon::parse($oldStart)->format('d M Y, h:i A')
                    . ' to '
                    . \Carbon\Carbon::parse($oldEnd)->format('h:i A')
                    . '. New schedule: '
                    . $appointment->start_at->format('d M Y, h:i A')
                    . ' to '
                    . $appointment->end_at->format('h:i A')
                    . '.'
            );
        }
        
        return redirect()
            ->route('manager.appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');

    }

    
    public function cancel(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:2000'],
        ]);

        if (in_array($appointment->status, ['cancelled', 'completed', 'no_show'], true)) {
            return back()->with('error', 'This appointment cannot be cancelled.');
        }

        DB::transaction(function () use ($appointment, $data) {
            $appointment = Appointment::whereKey($appointment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array($appointment->status, ['cancelled', 'completed', 'no_show'], true)) {
                throw ValidationException::withMessages([
                    'appointment' => 'This appointment can no longer be cancelled.',
                ]);
            }

            $oldStatus = $appointment->status;

            $appointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $data['cancellation_reason'],
            ]);

            $appointment->history()->create([
                'user_id' => auth()->id(),
                'action' => 'cancelled',
                'old_status' => $oldStatus,
                'new_status' => 'cancelled',
                'notes' => $data['cancellation_reason'],
            ]);
        });

        // Notify assigned instructors after the transaction commits.
        $appointment->refresh();

        $this->notifyAssignedInstructors(
            $appointment,
            'Lesson Cancelled',
            'Your lesson for '
                . $appointment->course->name
                . ' scheduled for '
                . $appointment->start_at->format('d M Y')
                . ' at '
                . $appointment->start_at->format('h:i A')
                . ' has been cancelled. Reason: '
                . $appointment->cancellation_reason
        );

        return redirect()
            ->route('manager.appointments.show', $appointment)
            ->with('success', 'Appointment cancelled successfully.');
    }


        
    
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => ['required', 'in:confirmed,completed,no_show'],
        ]);

        $shouldNotify = false;

        $response = DB::transaction(function () use ($data, $appointment, &$shouldNotify) {

            $appointment = Appointment::whereKey($appointment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array($appointment->status, ['cancelled', 'completed', 'no_show'], true)) {
                return back()->with('error', 'This appointment is already closed.');
            }

            $newStatus = $data['status'];

            /*
            |--------------------------------------------------------------------------
            | Confirmation checks
            |--------------------------------------------------------------------------
            */

            if ($newStatus === 'confirmed') {

                $course = Course::with('instruments')
                    ->findOrFail($appointment->course_id);

                if (!$course->is_active) {
                    throw ValidationException::withMessages([
                        'appointment' => 'This course is no longer active.',
                    ]);
                }

                if (!in_array($course->lesson_type, [$appointment->appointment_type, 'both'], true)) {
                    throw ValidationException::withMessages([
                        'appointment' => 'This course does not support the appointment type.',
                    ]);
                }

                $start = Carbon::parse($appointment->start_at);
                $end = Carbon::parse($appointment->end_at);

                if (!$start->isSameDay($end)) {
                    throw ValidationException::withMessages([
                        'appointment' => 'The appointment must start and finish on the same day.',
                    ]);
                }

                $instructorIds = $appointment->instructors()
                    ->pluck('instructors.id')
                    ->all();

                $studentIds = $appointment->students()
                    ->pluck('students.id')
                    ->all();

                if (empty($instructorIds)) {
                    throw ValidationException::withMessages([
                        'appointment' => 'Please assign at least one instructor before confirming this appointment.',
                    ]);
                }

                if (empty($studentIds)) {
                    throw ValidationException::withMessages([
                        'appointment' => 'At least one student must be assigned before confirmation.',
                    ]);
                }

                if (
                    $appointment->appointment_type === 'individual'
                    && count($studentIds) !== 1
                ) {
                    throw ValidationException::withMessages([
                        'appointment' => 'An individual appointment must have exactly one student.',
                    ]);
                }

                if (
                    !$appointment->capacity
                    || count($studentIds) > $appointment->capacity
                ) {
                    throw ValidationException::withMessages([
                        'appointment' => 'The appointment capacity is invalid or insufficient for the assigned students.',
                    ]);
                }

                $instructors = Instructor::whereIn('id', $instructorIds)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                $students = Student::whereIn('id', $studentIds)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                if (
                    $instructors->count() !== count($instructorIds)
                    || $instructors->contains(fn ($i) => $i->status !== 'active')
                ) {
                    throw ValidationException::withMessages([
                        'appointment' => 'One or more assigned instructors are inactive.',
                    ]);
                }

                if (
                    $students->count() !== count($studentIds)
                    || $students->contains(fn ($s) => $s->status !== 'active')
                ) {
                    throw ValidationException::withMessages([
                        'appointment' => 'One or more assigned students are inactive.',
                    ]);
                }

                $instrumentIds = $course->instruments->pluck('id');

                foreach ($instructors as $instructor) {

                    // Check instructor qualifications
                    if (
                        $instrumentIds->isNotEmpty()
                        && !$instructor->instruments()
                            ->whereIn('instruments.id', $instrumentIds)
                            ->exists()
                    ) {
                        throw ValidationException::withMessages([
                            'appointment' => "{$instructor->first_name} {$instructor->last_name} does not have the required course qualification.",
                        ]);
                    }

                    // Check weekly availability
                    $available = $instructor->availabilities()
                        ->where('day_of_week', $start->dayOfWeek)
                        ->where('is_available', true)
                        ->where('start_time', '<=', $start->format('H:i:s'))
                        ->where('end_time', '>=', $end->format('H:i:s'))
                        ->exists();

                    if (!$available) {
                        throw ValidationException::withMessages([
                            'appointment' => "{$instructor->first_name} {$instructor->last_name} is not available at the scheduled time.",
                        ]);
                    }

                    // Check other appointments
                    $conflict = Appointment::whereIn('status', ['pending', 'confirmed'])
                        ->where('id', '!=', $appointment->id)
                        ->where('start_at', '<', $end)
                        ->where('end_at', '>', $start)
                        ->whereHas('instructors', function ($query) use ($instructor) {
                            $query->where('instructors.id', $instructor->id);
                        })
                        ->exists();

                    if ($conflict) {
                        throw ValidationException::withMessages([
                            'appointment' => "{$instructor->first_name} {$instructor->last_name} has another appointment at that time.",
                        ]);
                    }

                    // Check vacation programme sessions
                    $programmeConflict = DB::table('programme_sessions')
                        ->where('instructor_id', $instructor->id)
                        ->where('session_date', $start->toDateString())
                        ->where('status', 'scheduled')
                        ->where('start_time', '<', $end->format('H:i:s'))
                        ->where('end_time', '>', $start->format('H:i:s'))
                        ->exists();

                    if ($programmeConflict) {
                        throw ValidationException::withMessages([
                            'appointment' => "{$instructor->first_name} {$instructor->last_name} has a programme session at that time.",
                        ]);
                    }
                }

                // Check student appointment conflicts
                foreach ($students as $student) {

                    $conflict = Appointment::whereIn('status', ['pending', 'confirmed'])
                        ->where('id', '!=', $appointment->id)
                        ->where('start_at', '<', $end)
                        ->where('end_at', '>', $start)
                        ->whereHas('students', function ($query) use ($student) {
                            $query->where('students.id', $student->id);
                        })
                        ->exists();

                    if ($conflict) {
                        throw ValidationException::withMessages([
                            'appointment' => "{$student->first_name} {$student->last_name} has another appointment at that time.",
                        ]);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Completion and no-show checks
            |--------------------------------------------------------------------------
            */

            if (
                in_array($newStatus, ['completed', 'no_show'], true)
                && now()->lessThan($appointment->end_at)
            ) {
                return back()->with(
                    'error',
                    'An appointment cannot be completed or marked as no-show before its scheduled end time.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Update appointment status and history
            |--------------------------------------------------------------------------
            */

            $oldStatus = $appointment->status;

            $appointment->update([
                'status' => $newStatus,
            ]);

            // Notify instructors only when a booking is newly confirmed.
            if ($oldStatus !== 'confirmed' && $newStatus === 'confirmed') {
                $shouldNotify = true;
            }

            $appointment->history()->create([
                'user_id' => auth()->id(),
                'action' => 'status_changed',
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'notes' => 'Appointment status updated by manager.',
            ]);

            return back()->with('success', 'Appointment status updated.');
        });
        
        if ($shouldNotify) {
            $appointment->refresh();
        
            $this->notifyAssignedInstructors(
                $appointment,
                'Booking Confirmed',
                'Your booking for '
                    . $appointment->course->name
                    . ' has been confirmed for '
                    . $appointment->start_at->format('d M Y')
                    . ' at '
                    . $appointment->start_at->format('h:i A')
                    . '.'
            );
        }
        
        return $response;        
    }

    public function requests()
    {
        $appointments = Appointment::with([
            'course',
            'students',
            'instructors',
            'requester',
        ])
            ->whereNotNull('requested_by')
            ->where('status', 'pending')
            ->latest('created_at')
            ->paginate(15);

        return view('manager.appointments.requests', compact('appointments'));
    }


    private function notifyAssignedInstructors(
        Appointment $appointment,
        string $title,
        string $message
    ): void {
        $appointment->loadMissing(['instructors', 'course']);

        $url = route('instructor.lessons.show', $appointment->id, false);
        
        foreach ($appointment->instructors as $instructor) {
            // Retrieve the actual User model through the relationship.
            $user = $instructor->user()->first();

            // Some instructors may not yet have login accounts.
            if (!$user) {
                continue;
            }

            $user->notify(
                new InstructorMessageNotification(
                    $title,
                    $message,
                    $url
                )
            );
        }
    }


}
