<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\ProgrammeSession;
use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProgrammeSessionController extends Controller
{
    public function create(Programme $programme)
    {
        $courses = Course::where('is_active', true)
            ->orderBy('name')
            ->get();

        $instructors = Instructor::where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('manager.programmes.sessions.create', compact(
            'programme',
            'courses',
            'instructors'
        ));
    }

    public function store(Request $request, Programme $programme)
    {
        $validated = $request->validate([
            'course_id' => [
                'required',
                'exists:courses,id'
            ],
            'instructor_id' => [
                'required',
                'exists:instructors,id'
            ],
            'session_date' => [
                'required',
                'date',
                'after_or_equal:' . $programme->start_date->format('Y-m-d'),
                'before_or_equal:' . $programme->end_date->format('Y-m-d'),
            ],
            'start_time' => [
                'required',
                'date_format:H:i'
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time'
            ],
            'capacity' => [
                'nullable',
                'integer',
                'min:1',
                'max:100'
            ],
            'location' => [
                'nullable',
                'string',
                'max:255'
            ],
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $instructor = Instructor::findOrFail($validated['instructor_id']);

        if (! $course->is_active) {
            throw ValidationException::withMessages([
                'course_id' => 'The selected course is inactive.',
            ]);
        }

        if ($instructor->status !== 'active') {
            throw ValidationException::withMessages([
                'instructor_id' => 'The selected instructor is inactive.',
            ]);
        }

        $courseInstrumentIds = $course->instruments()
            ->pluck('instruments.id');

        if (
            $courseInstrumentIds->isNotEmpty() &&
            ! $instructor->instruments()
                ->whereIn('instruments.id', $courseInstrumentIds)
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'instructor_id' => 'This instructor is not qualified for the selected course instruments.',
            ]);
        }

        // Check instructor's existing weekly availability.
        $dayOfWeek = \Carbon\Carbon::parse(
            $validated['session_date']
        )->dayOfWeek;

        $hasAvailability = $instructor->availabilities()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->where('start_time', '<=', $validated['start_time'])
            ->where('end_time', '>=', $validated['end_time'])
            ->exists();

        if (! $hasAvailability) {
            throw ValidationException::withMessages([
                'start_time' => 'The instructor is not available for the selected day and time.',
            ]);
        }

        // Check for overlapping sessions assigned to this instructor.
        $overlap = ProgrammeSession::where(
            'instructor_id',
            $instructor->id
        )
            ->where('session_date', $validated['session_date'])
            ->where('status', 'scheduled')
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'instructor_id' => 'This instructor already has a session during that time.',
            ]);
        }

        $programme->sessions()->create([
            ...$validated,
            'status' => 'scheduled',
        ]);

        return redirect()
            ->route('manager.programmes.show', $programme)
            ->with('success', 'Session scheduled successfully.');
    }


public function edit(Programme $programme, ProgrammeSession $session)
{
    abort_unless($session->programme_id === $programme->id, 404);
    abort_unless($session->status === 'scheduled', 404);

    $courses = Course::where('is_active', true)
        ->orderBy('name')
        ->get();

    $instructors = Instructor::where('status', 'active')
        ->orderBy('first_name')
        ->get();

    return view('manager.programmes.sessions.edit', compact(
        'programme',
        'session',
        'courses',
        'instructors'
    ));
}

public function update(
    Request $request,
    Programme $programme,
    ProgrammeSession $session
) {
    abort_unless($session->programme_id === $programme->id, 404);
    abort_unless($session->status === 'scheduled', 404);

    $validated = $request->validate([
        'course_id' => ['required', 'exists:courses,id'],
        'instructor_id' => ['required', 'exists:instructors,id'],
        'session_date' => [
            'required',
            'date',
            'after_or_equal:' . $programme->start_date->format('Y-m-d'),
            'before_or_equal:' . $programme->end_date->format('Y-m-d'),
        ],
        'start_time' => ['required', 'date_format:H:i'],
        'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        'capacity' => ['nullable', 'integer', 'min:1', 'max:100'],
        'location' => ['nullable', 'string', 'max:255'],
    ]);

    $course = Course::where('is_active', true)
        ->findOrFail($validated['course_id']);

    if (! in_array($course->lesson_type, ['group', 'both'])) {
        throw ValidationException::withMessages([
            'course_id' => 'Vacation programme sessions require a group-capable course.',
        ]);
    }

    $instructor = Instructor::where('status', 'active')
        ->findOrFail($validated['instructor_id']);

    $courseInstrumentIds = $course->instruments()->pluck('instruments.id');

    if (
        $courseInstrumentIds->isNotEmpty() &&
        ! $instructor->instruments()
            ->whereIn('instruments.id', $courseInstrumentIds)
            ->exists()
    ) {
        throw ValidationException::withMessages([
            'instructor_id' => 'This instructor is not qualified for the selected course instruments.',
        ]);
    }

    $dayOfWeek = \Carbon\Carbon::parse(
        $validated['session_date']
    )->dayOfWeek;

    $hasAvailability = $instructor->availabilities()
        ->where('day_of_week', $dayOfWeek)
        ->where('is_available', true)
        ->where('start_time', '<=', $validated['start_time'])
        ->where('end_time', '>=', $validated['end_time'])
        ->exists();

    if (! $hasAvailability) {
        throw ValidationException::withMessages([
            'start_time' => 'The instructor is not available for the selected day and time.',
        ]);
    }

    $overlap = ProgrammeSession::where('instructor_id', $instructor->id)
        ->where('session_date', $validated['session_date'])
        ->where('status', 'scheduled')
        ->where('id', '!=', $session->id)
        ->where('start_time', '<', $validated['end_time'])
        ->where('end_time', '>', $validated['start_time'])
        ->exists();

    if ($overlap) {
        throw ValidationException::withMessages([
            'instructor_id' => 'This instructor already has another session during that time.',
        ]);
    }

    $session->update($validated);

    return redirect()
        ->route('manager.programmes.show', $programme)
        ->with('success', 'Session updated successfully.');
}

    public function cancel(Programme $programme, ProgrammeSession $session)
    {
        abort_unless($session->programme_id === $programme->id, 404);

        if ($session->status === 'cancelled') {
            return back()->with('success', 'This session is already cancelled.');
        }

        $session->update(['status' => 'cancelled']);

        return back()->with('success', 'Session cancelled successfully.');
    }

}
