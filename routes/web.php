<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\StudentController;
use App\Http\Controllers\Manager\GuardianController;
use App\Http\Controllers\Manager\InstructorController;

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
