<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\Instrument;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InstructorController extends Controller
{
    public function index()
    {
        $instructors = Instructor::with('instruments')
            ->latest()
            ->paginate(10);

        return view('manager.instructors.index', compact('instructors'));
    }

    public function create()
    {
        $instruments = Instrument::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('manager.instructors.create', compact('instruments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string'],
            'hire_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
            'instruments' => ['nullable', 'array'],
            'instruments.*' => ['integer', 'distinct', 'exists:instruments,id'],
        ]);

        $instrumentIds = $validated['instruments'] ?? [];
        unset($validated['instruments']);

        $instructor = Instructor::create($validated);
        $instructor->instruments()->sync($instrumentIds);

        return redirect()
            ->route('manager.instructors.index')
            ->with('success', 'Instructor registered successfully.');
    }

    public function show(Instructor $instructor)
    {
        $instructor->load('instruments');

        return view('manager.instructors.show', compact('instructor'));
    }

    public function edit(Instructor $instructor)
    {
        $instruments = Instrument::where('is_active', true)
            ->orderBy('name')
            ->get();

        $instructor->load('instruments');

        return view('manager.instructors.edit', compact(
            'instructor',
            'instruments'
        ));
    }

    public function update(Request $request, Instructor $instructor)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string'],
            'hire_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
            'instruments' => ['nullable', 'array'],
            'instruments.*' => ['integer', 'distinct', 'exists:instruments,id'],
        ]);

        $instrumentIds = $validated['instruments'] ?? [];
        unset($validated['instruments']);

        $instructor->update($validated);
        $instructor->instruments()->sync($instrumentIds);

        return redirect()
            ->route('manager.instructors.index')
            ->with('success', 'Instructor details updated successfully.');
    }
}
