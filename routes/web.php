<?php

use Illuminate\Support\Facades\Route;

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


Route::middleware(['auth', 'role:manager'])->group(function () {

    Route::view('/manager/dashboard', 'dashboards.manager')
        ->name('manager.dashboard');

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
