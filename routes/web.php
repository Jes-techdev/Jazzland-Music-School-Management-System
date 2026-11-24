<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\StudentController;
use App\Http\Controllers\Manager\GuardianController;
use App\Http\Controllers\Manager\InstructorController;
use App\Http\Controllers\Manager\InstructorAvailabilityController;
use App\Http\Controllers\Manager\CourseController;
use App\Http\Controllers\Manager\ProgrammeSessionController;
use App\Http\Controllers\Manager\ProgrammeEnrolmentController;
use App\Http\Controllers\Manager\AppointmentController;

Route::get('/', function(){
    return view('welcome');
});

Route::get('/dashboard', function () {

    $role = auth()->user()->role?->name;

    return match ($role) {
        'manager' => redirect()->route('manager.dashboard'),
        'instructor' => redirect()->route('instructor.dashboard'),
        'parent' => redirect()->route('parent.dashboard'),
        'student' => redirect()->route('student.dashboard'),
        default => abort(403),
    };

})->middleware('auth')->name('dashboard');


Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

        Route::view('/dashboard', 'dashboards.manager')
            ->name('dashboard');
        Route::resource('students', StudentController::class)
        ->except(['destroy']);

        Route::resource('guardians', GuardianController::class)
        ->except(['destroy']);

        Route::get('/guardians/{guardian}/students',
            [GuardianController::class, 'editStudents'])
            ->name('guardians.students.edit');

        Route::put('/guardians/{guardian}/students',
            [GuardianController::class, 'updateStudents'])
            ->name('guardians.students.update');

        Route::resource('instructors', InstructorController::class)
        ->except(['destroy']);

        Route::get(
            'instructors/{instructor}/availability',
            [InstructorAvailabilityController::class, 'index']
        )->name('instructors.availability.index');
        
        Route::post(
            'instructors/{instructor}/availability',
            [InstructorAvailabilityController::class, 'store']
        )->name('instructors.availability.store');
        
        Route::delete(
            'instructors/{instructor}/availability/{availability}',
            [InstructorAvailabilityController::class, 'destroy']
        )->name('instructors.availability.destroy');

        Route::resource('courses', CourseController::class)
        ->except(['destroy']);

        Route::resource('programmes', \App\Http\Controllers\Manager\ProgrammeController::class)
        ->except('destroy');

        Route::patch('programmes/{programme}/status',
            [\App\Http\Controllers\Manager\ProgrammeController::class, 'updateStatus']
        )->name('programmes.status');

        Route::get(
            'programmes/{programme}/sessions/create',
            [ProgrammeSessionController::class, 'create']
        )->name('programmes.sessions.create');
        
        Route::post(
            'programmes/{programme}/sessions',
            [ProgrammeSessionController::class, 'store']
        )->name('programmes.sessions.store');

        Route::get(
            'programmes/{programme}/sessions/{session}/edit',
            [ProgrammeSessionController::class, 'edit']
        )->name('programmes.sessions.edit');
        
        Route::put(
            'programmes/{programme}/sessions/{session}',
            [ProgrammeSessionController::class, 'update']
        )->name('programmes.sessions.update');
        
        Route::patch(
            'programmes/{programme}/sessions/{session}/cancel',
            [ProgrammeSessionController::class, 'cancel']
        )->name('programmes.sessions.cancel');

        Route::get(
            'programmes/{programme}/enrolments',
            [ProgrammeEnrolmentController::class, 'index']
        )->name('programmes.enrolments.index');
        
        Route::get(
            'programmes/{programme}/enrolments/create',
            [ProgrammeEnrolmentController::class, 'create']
        )->name('programmes.enrolments.create');
        
        Route::post(
            'programmes/{programme}/enrolments',
            [ProgrammeEnrolmentController::class, 'store']
        )->name('programmes.enrolments.store');
        
        Route::patch(
            'programmes/{programme}/enrolments/{enrolment}/status',
            [ProgrammeEnrolmentController::class, 'updateStatus']
        )->name('programmes.enrolments.status');

        Route::resource('appointments', AppointmentController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update']);

        Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
            ->name('appointments.cancel');

        Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
            ->name('appointments.status');

});



Route::middleware(['auth', 'role:instructor'])->group(function () {

    Route::view('/instructor/dashboard', 'dashboards.instructor')
        ->name('instructor.dashboard');

});


Route::middleware(['auth', 'role:parent'])->group(function () {

    Route::view('/parent/dashboard', 'dashboards.parent')
        ->name('parent.dashboard');

});


Route::middleware(['auth', 'role:student'])->group(function () {

    Route::view('/student/dashboard', 'dashboards.student')
        ->name('student.dashboard');

});
