<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Instrument;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('instruments')
            ->latest()
            ->paginate(10);

        return view('manager.courses.index', compact('courses'));
    }

    public function create()
    {
        $instruments = Instrument::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('manager.courses.create', compact('instruments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'lesson_type' => ['required', 'in:individual,group,both'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'default_capacity' => [
                'nullable',
                'required_if:lesson_type,group,both',
                'integer',
                'min:2',
                'max:100'
            ],
            'is_active' => ['required', 'boolean'],
            'instruments' => ['nullable', 'array'],
            'instruments.*' => [
                'integer',
                'distinct',
                'exists:instruments,id'
            ],
        ]);

        $instrumentIds = $validated['instruments'] ?? [];
        unset($validated['instruments']);

        $course = Course::create($validated);
        $course->instruments()->sync($instrumentIds);

        return redirect()
            ->route('manager.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load('instruments');

        return view('manager.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $instruments = Instrument::where('is_active', true)
            ->orderBy('name')
            ->get();

        $course->load('instruments');

        return view('manager.courses.edit', compact(
            'course',
            'instruments'
        ));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'lesson_type' => ['required', 'in:individual,group,both'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'default_capacity' => [
                'nullable',
                'required_if:lesson_type,group,both',
                'integer',
                'min:2',
                'max:100'
            ],
            'is_active' => ['required', 'boolean'],
            'instruments' => ['nullable', 'array'],
            'instruments.*' => [
                'integer',
                'distinct',
                'exists:instruments,id'
            ],
        ]);

        $instrumentIds = $validated['instruments'] ?? [];
        unset($validated['instruments']);

        $course->update($validated);
        $course->instruments()->sync($instrumentIds);

        return redirect()
            ->route('manager.courses.index')
            ->with('success', 'Course updated successfully.');
    }
}
