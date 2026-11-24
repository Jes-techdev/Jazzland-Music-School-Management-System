<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\InstructorAvailability;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InstructorAvailabilityController extends Controller
{
    public function index(Instructor $instructor)
    {
        $availabilities = $instructor->availabilities()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('manager.instructors.availability', compact(
            'instructor',
            'availabilities'
        ));
    }

    public function store(Request $request, Instructor $instructor)
    {
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

        return back()->with('success', 'Availability period added.');
    }

    public function destroy(
        Instructor $instructor,
        InstructorAvailability $availability
    ) {
        // Ensure this availability belongs to the selected instructor.
        $instructor->availabilities()
            ->findOrFail($availability->id)
            ->delete();

        return back()->with('success', 'Availability period removed.');
    }
}
