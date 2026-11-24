<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\ProgrammeEnrolment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class ProgrammeEnrolmentController extends Controller
{
    public function index(Programme $programme)
    {
        $enrolments = $programme->enrolments()
            ->with('student')
            ->latest()
            ->paginate(15);

        return view(
            'manager.programmes.enrolments.index',
            compact('programme', 'enrolments')
        );
    }

    public function create(Programme $programme)
    {
        $students = Student::where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view(
            'manager.programmes.enrolments.create',
            compact('programme', 'students')
        );
    }

    public function store(Request $request, Programme $programme)
    {
        $validated = $request->validate([
            'student_id' => [
                'required',
                'exists:students,id'
            ],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $student = Student::where('status', 'active')
            ->findOrFail($validated['student_id']);

        DB::transaction(function () use ($programme, $student, $validated) {
            $lockedProgramme = Programme::whereKey($programme->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedProgramme->status !== 'published') {
                throw ValidationException::withMessages([
                    'student_id' => 'Students can only be enrolled in published programmes.',
                ]);
            }

            $alreadyEnrolled = ProgrammeEnrolment::where(
                'programme_id',
                $lockedProgramme->id
            )
                ->where('student_id', $student->id)
                ->exists();

            if ($alreadyEnrolled) {
                throw ValidationException::withMessages([
                    'student_id' => 'This student already has an enrolment record for this programme.',
                ]);
            }

            if ($lockedProgramme->capacity !== null) {
                $confirmedCount = ProgrammeEnrolment::where(
                    'programme_id',
                    $lockedProgramme->id
                )
                    ->where('status', 'confirmed')
                    ->count();

                if ($confirmedCount >= $lockedProgramme->capacity) {
                    throw ValidationException::withMessages([
                        'student_id' => 'This programme has reached its maximum capacity.',
                    ]);
                }
            }

            ProgrammeEnrolment::create([
                'programme_id' => $lockedProgramme->id,
                'student_id' => $student->id,
                'status' => 'confirmed',
                'enrolled_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('manager.programmes.enrolments.index', $programme)
            ->with('success', 'Student enrolled successfully.');
    }

    public function updateStatus(
        Request $request,
        Programme $programme,
        ProgrammeEnrolment $enrolment
    ) {
        abort_unless($enrolment->programme_id === $programme->id, 404);

        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,confirmed,cancelled,completed'
            ],
        ]);

        DB::transaction(function () use (
            $programme,
            $enrolment,
            $validated
        ) {
            $lockedProgramme = Programme::whereKey($programme->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedEnrolment = ProgrammeEnrolment::whereKey($enrolment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($validated['status'] === 'confirmed') {
                if ($lockedProgramme->status !== 'published') {
                    throw ValidationException::withMessages([
                        'status' => 'Only published programmes can accept confirmed enrolments.',
                    ]);
                }

                if (
                    $lockedEnrolment->status !== 'confirmed' &&
                    $lockedProgramme->capacity !== null
                ) {
                    $confirmedCount = ProgrammeEnrolment::where(
                        'programme_id',
                        $lockedProgramme->id
                    )
                        ->where('status', 'confirmed')
                        ->count();

                    if ($confirmedCount >= $lockedProgramme->capacity) {
                        throw ValidationException::withMessages([
                            'status' => 'The programme has reached its maximum capacity.',
                        ]);
                    }
                }
            }

            $lockedEnrolment->update([
                'status' => $validated['status'],
                'enrolled_at' => $validated['status'] === 'confirmed'
                    ? ($lockedEnrolment->enrolled_at ?? now())
                    : $lockedEnrolment->enrolled_at,
            ]);
        });

        return back()->with('success', 'Enrolment status updated.');
    }
}
