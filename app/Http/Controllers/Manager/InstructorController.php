<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\Instrument;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

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

    public function createAccount(Request $request, Instructor $instructor)
    {
        // Ensure the instructor does not already have an account.
        if ($instructor->user_id !== null) {
            return back()->with('error', 'This instructor already has a login account.');
        }

        // An email address is required for login.
        if (empty($instructor->email)) {
            return back()->with('error', 'Add an email address to the instructor profile first.');
        }

        // Ensure the email is not already registered.
        if (User::where('email', $instructor->email)->exists()) {
            return back()->with('error', 'This email address is already associated with another user account.');
        }

        // Find the instructor role.
        $role = Role::where('name', 'instructor')->first();

        if (!$role) {
            return back()->with('error', 'Instructor role not found. Please check the roles table.');
        }

        DB::transaction(function () use ($instructor, $role) {

            // Lock the instructor record to prevent simultaneous account creation.
            $lockedInstructor = Instructor::whereKey($instructor->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedInstructor->user_id !== null) {
                throw new \RuntimeException('This instructor already has a login account.');
            }

            // Create the user account.
            $user = User::create([
                'name' => $lockedInstructor->first_name . ' ' . $lockedInstructor->last_name,
                'email' => $lockedInstructor->email,
                'password' => Str::random(40),
                'role_id' => $role->id,
            ]);

            // Link the new account to the instructor profile.
            $lockedInstructor->user_id = $user->id;
            $lockedInstructor->save();
        });

        // Send password setup link.
        $status = Password::sendResetLink([
            'email' => $instructor->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()
                ->route('manager.instructors.show', $instructor)
                ->with('success', 'Instructor account created and password setup email generated successfully.');
        }

        return redirect()
            ->route('manager.instructors.show', $instructor)
            ->with('error', 'Account created, but the password setup email could not be generated: ' . __($status));
    }

    public function sendPasswordSetup(Instructor $instructor)
    {
        // Load the linked user account.
        $instructor->load('user');

        if (!$instructor->user) {
            return back()->with(
                'error',
                'This instructor does not have a linked login account.'
            );
        }

        // Generate and send a password setup link.
        $status = Password::sendResetLink([
            'email' => $instructor->user->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'success',
                'Password setup email generated successfully.'
            );
        }

        return back()->with(
            'error',
            'Unable to generate password setup link: ' . __($status)
        );
    }
}
