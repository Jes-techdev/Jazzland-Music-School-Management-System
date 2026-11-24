<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use Illuminate\Http\Request;

class ProgrammeController extends Controller
{
    public function index()
    {
        $programmes = Programme::withCount([
            'sessions',
            'enrolments'
        ])->latest()->paginate(10);

        return view('manager.programmes.index', compact('programmes'));
    }

    public function create()
    {
        return view('manager.programmes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date'
            ],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $validated['status'] = 'draft';

        $programme = Programme::create($validated);

        return redirect()
            ->route('manager.programmes.show', $programme)
            ->with('success', 'Programme created successfully.');
    }

    public function show(Programme $programme)
    {
        $programme->load([
            'sessions.course',
            'sessions.instructor',
        ]);

        $programme->loadCount('enrolments');

        return view('manager.programmes.show', compact('programme'));
    }

    public function edit(Programme $programme)
    {
        return view('manager.programmes.edit', compact('programme'));
    }

    public function update(Request $request, Programme $programme)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date'
            ],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $programme->update($validated);

        return redirect()
            ->route('manager.programmes.show', $programme)
            ->with('success', 'Programme updated successfully.');
    }

    public function updateStatus(Request $request, Programme $programme)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:draft,published,ongoing,completed,cancelled'
            ],
        ]);

        $programme->update($validated);

        return back()->with('success', 'Programme status updated.');
    }
}
