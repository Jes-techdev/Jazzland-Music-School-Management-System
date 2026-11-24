<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ParentPortalController extends Controller
{
    private function guardian()
    {
        return auth()->user()->guardian()->firstOrFail();
    }

    public function dashboard()
    {
        $guardian = $this->guardian();

        $students = $guardian->students()
            ->orderBy('first_name')
            ->get();

        return view('parent.dashboard', compact('guardian', 'students'));
    }

    public function profile()
    {
        $guardian = $this->guardian();

        return view('parent.profile', compact('guardian'));
    }

    public function updateProfile(Request $request)
    {
        $guardian = $this->guardian();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(auth()->id()),
            ],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($guardian, $data) {
            $guardian->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'] ?? null,
            ]);

            auth()->user()->update([
                'name' => trim($data['first_name'] . ' ' . $data['last_name']),
                'email' => $data['email'],
            ]);
        });

        return back()->with('success', 'Your profile has been updated.');
    }

    public function students()
    {
        $students = $this->guardian()
            ->students()
            ->orderBy('first_name')
            ->paginate(15);

        return view('parent.students.index', compact('students'));
    }

    public function createStudent()
    {
        return view('parent.students.create');
    }

    public function storeStudent(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'relationship_type' => [
                'required',
                'in:mother,father,guardian,grandparent,other',
            ],
        ]);

        DB::transaction(function () use ($data) {
            $student = Student::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'status' => 'active',
            ]);

            $this->guardian()->students()->attach($student->id, [
                'relationship_type' => $data['relationship_type'],
                'is_primary' => true,
            ]);
        });

        return redirect()
            ->route('parent.students.index')
            ->with('success', 'Child registered successfully.');
    }

    public function editStudent(Student $student)
    {
        $guardian = $this->guardian();

        abort_unless(
            $guardian->students()->where('students.id', $student->id)->exists(),
            404
        );

        $relationship = $guardian->students()
            ->where('students.id', $student->id)
            ->first()
            ->pivot
            ->relationship_type;

        return view('parent.students.edit', compact(
            'student',
            'relationship'
        ));
    }

    public function updateStudent(Request $request, Student $student)
    {
        $guardian = $this->guardian();

        abort_unless(
            $guardian->students()->where('students.id', $student->id)->exists(),
            404
        );

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'relationship_type' => [
                'required',
                'in:mother,father,guardian,grandparent,other',
            ],
        ]);

        DB::transaction(function () use ($student, $guardian, $data) {
            $student->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
            ]);

            $guardian->students()->updateExistingPivot($student->id, [
                'relationship_type' => $data['relationship_type'],
            ]);
        });

        return redirect()
            ->route('parent.students.index')
            ->with('success', 'Child information updated successfully.');
    }
}
