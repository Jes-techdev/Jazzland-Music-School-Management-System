<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class InstructorProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $instructor = $user->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        $instructor->load('instruments');

        return view('instructor.profile.index', compact('instructor'));
    }

    public function edit()
    {
        $user = auth()->user();

        $instructor = $user->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        return view('instructor.profile.edit', compact('instructor'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $instructor = $user->instructor;

        if (!$instructor) {
            abort(403, 'No instructor profile is linked to this account.');
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:3000'],
        ]);

        DB::transaction(function () use ($user, $instructor, $validated) {

            $instructor->update($validated);

            $user->update([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
            ]);

        });

        return redirect()
            ->route('instructor.profile')
            ->with('success', 'Your profile has been updated successfully.');
    }


    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $user->update([
            'password' => $validated['password'],
        ]);

        return back()->with(
            'password_success',
            'Your password has been changed successfully.'
        );
    }

}
