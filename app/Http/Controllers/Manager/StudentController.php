<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('guardians')
            ->latest()
            ->paginate(10);

        return view('manager.students.index', compact('students'));
    }

    public function create()
    {
        return view('manager.students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Student::create($validated);

        return redirect()
            ->route('manager.students.index')
            ->with('success', 'Student registered successfully.');
    }

    public function show(Student $student)
    {
        $student->load('guardians');

        return view('manager.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('manager.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $student->update($validated);

        return redirect()
            ->route('manager.students.index')
            ->with('success', 'Student details updated successfully.');
    }
}
