<?php

namespace App\Http\Controllers;

use App\Models\Programme;
use App\Models\ProgrammeEnrolment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParentProgrammeEnrolmentController extends Controller
{
    private function guardian()
    {
        return auth()->user()->guardian()->firstOrFail();
    }

    public function create(Programme $programme)
    {
        abort_unless(
            $programme->status === 'published'
            && $programme->end_date >= now()->toDateString()
            && $programme->sessions()
                ->where('status', 'scheduled')->exists(),
            404
        );

        $students = $this->guardian()->students()
            ->where('students.status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('parent.programmes.enrol', compact(
            'programme',
            'students'
        ));
    }

    // public function create(Programme $programme)
    // {
    //     return response()->json([
    //         'message' => 'Programme loaded successfully',
    //         'programme_id' => $programme->id,
    //         'programme_title' => $programme->title,
    //         'user_id' => auth()->id(),
    //         'guardian_id' => auth()->user()->guardian?->id,
    //     ]);
    // }
    // public function create(Programme $programme)
    // {
    //     abort_unless(
    //         $programme->status === 'published'
    //         && $programme->end_date >= now()->toDateString()
    //         && $programme->sessions()
    //             ->where('status', 'scheduled')->exists(),
    //         404
    //     );

    //     $students = $this->guardian()->students()
    //         ->where('students.status', 'active')
    //         ->orderBy('first_name')
    //         ->get();

    //     return response()->json([
    //         'programme' => $programme->title,
    //         'students' => $students->pluck('first_name'),
    //     ]);
    // }

    public function store(Request $request, Programme $programme)
    {
        abort_unless(
            $programme->status === 'published'
            && $programme->end_date >= now()->toDateString()
            && $programme->sessions()
                ->where('status', 'scheduled')->exists(),
            404
        );

        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $guardian = $this->guardian();

        abort_unless(
            $guardian->students()
                ->where('students.id', $data['student_id'])
                ->where('students.status', 'active')
                ->exists(),
            403
        );

        DB::transaction(function () use ($programme, $data) {
            $lockedProgramme = Programme::whereKey($programme->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $lockedProgramme->status === 'published'
                && $lockedProgramme->end_date >= now()->toDateString(),
                422,
                'This programme is no longer accepting enrolments.'
            );

            $enrolment = ProgrammeEnrolment::where(
                'programme_id', $lockedProgramme->id
            )->where('student_id', $data['student_id'])
                ->lockForUpdate()
                ->first();

            if ($enrolment) {
                if ($enrolment->status !== 'cancelled') {
                    abort(422, 'This student already has an enrolment for this programme.');
                }

                $enrolment->update([
                    'status' => 'pending',
                    'enrolled_at' => now(),
                    'notes' => $data['notes'] ?? null,
                ]);

                return;
            }

            $confirmedCount = ProgrammeEnrolment::where(
                'programme_id', $lockedProgramme->id
            )->whereIn('status', ['confirmed', 'completed'])->count();
            
            if (
                $lockedProgramme->capacity !== null
                && $confirmedCount >= $lockedProgramme->capacity
            ) {
                abort(422, 'This programme is currently full.');
            }

            ProgrammeEnrolment::create([
                'programme_id' => $lockedProgramme->id,
                'student_id' => $data['student_id'],
                'status' => 'pending',
                'enrolled_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return redirect()->route('parent.programmes.index')
            ->with('success', 'Your enrolment request has been submitted for approval.');
    }

    public function index()
    {
        $enrolments = $this->guardian()->students()
            ->with(['programmeEnrolments.programme'])
            ->get()
            ->flatMap(function ($student) {
                return $student->programmeEnrolments;
            })
            ->sortByDesc('enrolled_at');

        return view('parent.programmes.index', compact('enrolments'));
    }
}
