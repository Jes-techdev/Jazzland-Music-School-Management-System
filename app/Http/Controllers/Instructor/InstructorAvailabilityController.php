<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorAvailability;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InstructorAvailabilityController extends Controller
{
    /**
     * Display the logged-in instructor's weekly availability.
     */
    public function index()
    {
        $instructor = auth()->user()->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        $availabilities = $instructor->availabilities()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('instructor.availability.index', compact(
            'instructor',
            'availabilities'
        ));
    }

    /**
     * Add a new availability period.
     */
    public function store(Request $request)
    {
        $instructor = auth()->user()->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        $validated = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $overlap = $instructor->availabilities()
            ->where('day_of_week', $validated['day_of_week'])
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'start_time' => 'This period overlaps an existing availability period.',
            ]);
        }

        $instructor->availabilities()->create($validated);

        return back()->with('success', 'Your availability has been added successfully.');
    }

    /**
     * Remove an availability period.
     */
    public function destroy(InstructorAvailability $availability)
    {
        $instructor = auth()->user()->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        // Ensure the availability belongs to the logged-in instructor.
        $availability = $instructor->availabilities()
            ->findOrFail($availability->id);

        // Prevent removal if a future confirmed lesson falls
        // within this weekly availability period.
        $dayOfWeek = $availability->day_of_week;

        $hasConfirmedLessons = $instructor->appointments()
            ->where('appointments.status', 'confirmed')
            ->where('appointments.start_at', '>', now())
            ->whereRaw('DAYOFWEEK(appointments.start_at) - 1 = ?', [$dayOfWeek])
            ->whereTime('appointments.start_at', '>=', $availability->start_time)
            ->whereTime('appointments.end_at', '<=', $availability->end_time)
            ->exists();

        if ($hasConfirmedLessons) {
            return back()->with(
                'error',
                'This availability cannot be removed because it contains a future confirmed lesson.'
            );
        }

        $availability->delete();

        return back()->with(
            'success',
            'Your availability has been removed successfully.'
        );
    }
}
