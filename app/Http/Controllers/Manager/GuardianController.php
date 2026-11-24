<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
    public function index()
    {
        $guardians = Guardian::withCount('students')
            ->latest()
            ->paginate(10);

        return view('manager.guardians.index', compact('guardians'));
    }

    public function create()
    {
        return view('manager.guardians.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        Guardian::create($validated);

        return redirect()
            ->route('manager.guardians.index')
            ->with('success', 'Guardian registered successfully.');
    }

    public function show(Guardian $guardian)
    {
        $guardian->load('students');

        return view('manager.guardians.show', compact('guardian'));
    }

    public function edit(Guardian $guardian)
    {
        return view('manager.guardians.edit', compact('guardian'));
    }

    public function update(Request $request, Guardian $guardian)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $guardian->update($validated);

        return redirect()
            ->route('manager.guardians.index')
            ->with('success', 'Guardian details updated successfully.');
    }

    public function editStudents(Guardian $guardian)
    {
        $students = Student::where('status', 'active')
            ->orderBy('first_name')
            ->get();

        $guardian->load('students');

        return view('manager.guardians.students', compact(
            'guardian',
            'students'
        ));
    }

    public function updateStudents(Request $request, Guardian $guardian)
    {
        $validated = $request->validate([
            'students' => ['nullable', 'array'],
            'students.*' => ['integer', 'distinct', 'exists:students,id'],
            'relationships' => ['nullable', 'array'],
            'relationships.*' => ['nullable', 'in:mother,father,guardian,grandparent,other'],
            'primary_student_id' => ['nullable', 'integer', 'exists:students,id'],
        ]);

        $studentIds = $validated['students'] ?? [];

        if (
            isset($validated['primary_student_id']) &&
            !in_array($validated['primary_student_id'], $studentIds)
        ) {
            return back()->withErrors([
                'primary_student_id' => 'The primary student must be selected.',
            ]);
        }

        $syncData = [];

        foreach ($studentIds as $studentId) {
            $syncData[$studentId] = [
                'relationship_type' => $validated['relationships'][$studentId] ?? 'guardian',
                'is_primary' => $studentId == ($validated['primary_student_id'] ?? null),
            ];
        }

        $guardian->students()->sync($syncData);

        return redirect()
            ->route('manager.guardians.show', $guardian)
            ->with('success', 'Guardian-student relationships updated.');
    }
}
