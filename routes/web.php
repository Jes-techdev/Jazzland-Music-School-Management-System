<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\StudentController;
use App\Http\Controllers\Manager\GuardianController;
use App\Http\Controllers\Manager\InstructorController;
use App\Http\Controllers\Manager\InstructorAvailabilityController;
use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Http\Controllers\Manager\CourseController;
use App\Http\Controllers\Manager\ProgrammeSessionController;
use App\Http\Controllers\Manager\ProgrammeEnrolmentController;
use App\Http\Controllers\Manager\AppointmentController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\ParentProgrammeEnrolmentController;
use App\Http\Controllers\ParentAppointmentController;

// Route::get('/', function(){
//     return view('welcome');
// });

Route::get('/', [PublicBookingController::class, 'index'])
    ->name('public.home');

Route::get('/courses/{course}', [PublicBookingController::class, 'showCourse'])
    ->name('public.courses.show');

Route::get('/programmes/{programme}', [PublicBookingController::class, 'showProgramme'])
    ->name('public.programmes.show');

    
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

        Route::post(
            'instructors/{instructor}/create-account',
            [InstructorController::class, 'createAccount']
        )->name('instructors.create-account');

        Route::post(
            'instructors/{instructor}/send-password-setup',
            [InstructorController::class, 'sendPasswordSetup']
        )->name('instructors.send-password-setup');

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

        Route::get(
            'appointments/requests',
            [AppointmentController::class, 'requests']
        )->name('appointments.requests');
        
        Route::resource('appointments', AppointmentController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update']);

        Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
            ->name('appointments.cancel');

        Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
            ->name('appointments.status');

        

});



Route::middleware(['auth', 'role:instructor'])->group(function () {

    Route::get(
        '/instructor/dashboard',
        [\App\Http\Controllers\Instructor\InstructorDashboardController::class, 'index']
    )->name('instructor.dashboard');

    Route::get(
        '/instructor/lessons',
        [\App\Http\Controllers\Instructor\InstructorDashboardController::class, 'lessons']
    )->name('instructor.lessons');

    Route::get(
        '/instructor/lessons/{appointmentId}',
        [\App\Http\Controllers\Instructor\InstructorDashboardController::class, 'showLesson']
    )->name('instructor.lessons.show');

    // Instructor Availability
    Route::get(
        '/instructor/availability',
        [\App\Http\Controllers\Instructor\InstructorAvailabilityController::class, 'index']
    )->name('instructor.availability');

    Route::post(
        '/instructor/availability',
        [\App\Http\Controllers\Instructor\InstructorAvailabilityController::class, 'store']
    )->name('instructor.availability.store');

    Route::delete(
        '/instructor/availability/{availability}',
        [\App\Http\Controllers\Instructor\InstructorAvailabilityController::class, 'destroy']
    )->name('instructor.availability.destroy');

    Route::get('/lessons/{appointmentId}/attendance', [
        InstructorDashboardController::class,
        'attendance'
    ])->name('instructor.lessons.attendance');
    
    Route::post('/lessons/{appointmentId}/attendance', [
        InstructorDashboardController::class,
        'saveAttendance'
    ])->name('instructor.lessons.attendance.save');

    Route::post('/lessons/{appointmentId}/complete', [
        InstructorDashboardController::class,
        'completeLesson'
    ])->name('instructor.lessons.complete');

    Route::get('/attendance-history', [
        InstructorDashboardController::class,
        'attendanceHistory'
    ])->name('instructor.attendance-history');

    Route::get('/calendar', [
        InstructorDashboardController::class,
        'calendar'
    ])->name('instructor.calendar');
    
    Route::get('/calendar/events', [
        InstructorDashboardController::class,
        'calendarEvents'
    ])->name('instructor.calendar.events');

});



Route::middleware(['auth', 'role:parent'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {

        Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/profile', [ParentPortalController::class, 'profile'])
            ->name('profile');

        Route::put('/profile', [ParentPortalController::class, 'updateProfile'])
            ->name('profile.update');

        Route::get('/students', [ParentPortalController::class, 'students'])
            ->name('students.index');

        Route::get('/students/create', [ParentPortalController::class, 'createStudent'])
            ->name('students.create');

        Route::post('/students', [ParentPortalController::class, 'storeStudent'])
            ->name('students.store');

        Route::get('/students/{student}/edit', [ParentPortalController::class, 'editStudent'])
            ->name('students.edit');

        Route::put('/students/{student}', [ParentPortalController::class, 'updateStudent'])
            ->name('students.update');
        
        Route::get('/programmes', [
            ParentProgrammeEnrolmentController::class, 'index'
        ])->name('programmes.index');
        
        Route::get('/programmes/{programme}/enrol', [
            ParentProgrammeEnrolmentController::class, 'create'
        ])->name('programmes.enrol');
        
        Route::post('/programmes/{programme}/enrol', [
            ParentProgrammeEnrolmentController::class, 'store'
        ])->name('programmes.enrol.store');

        Route::get('/appointments', [
            ParentAppointmentController::class, 'index'
        ])->name('appointments.index');
        
        Route::get('/appointments/create', [
            ParentAppointmentController::class, 'create'
        ])->name('appointments.create');
        
        Route::post('/appointments', [
            ParentAppointmentController::class, 'store'
        ])->name('appointments.store');

        Route::get('/appointments/{appointment}', [
            ParentAppointmentController::class, 'show'
        ])->name('appointments.show');
        
        Route::post('/appointments/{appointment}/cancel', [
            ParentAppointmentController::class, 'cancel'
        ])->name('appointments.cancel');
});



Route::middleware(['auth', 'role:student'])->group(function () {

    Route::view('/student/dashboard', 'dashboards.student')
        ->name('student.dashboard');

});
