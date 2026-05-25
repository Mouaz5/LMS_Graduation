<?php

use App\Http\Controllers\Web\AcademicYearWebController;
use App\Http\Controllers\Web\SubjectWebController;
use App\Http\Controllers\Web\AdminUserController;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\CalendarWebController;
use App\Http\Controllers\Web\ClassroomWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ScheduleWebController;
use App\Http\Controllers\Web\AssignmentWebController;
use App\Http\Controllers\Web\ParentWebController;
use App\Http\Controllers\Web\SettingsWebController;
use App\Http\Controllers\Web\StudentWebController;
use App\Http\Controllers\Web\TeacherWebController;
use App\Http\Controllers\Web\TeacherAttendanceController;
use App\Http\Controllers\Web\TeacherBehavioralNoteController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

// Password reset
Route::get('/forgot-password', [AuthWebController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthWebController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthWebController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthWebController::class, 'resetPassword'])->name('password.update');

// Authenticated
Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/impersonate', [DashboardController::class, 'impersonate'])->name('impersonate');
    Route::post('/dashboard/stop-impersonate', [DashboardController::class, 'stopImpersonate'])->name('impersonate.stop');

    // Admin user management
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/link-parent', [AdminUserController::class, 'linkParent'])->name('users.link-parent');
        Route::delete('/users/{user}/unlink-parent', [AdminUserController::class, 'unlinkParent'])->name('users.unlink-parent');
        Route::post('/users/{user}/link-child', [AdminUserController::class, 'linkChild'])->name('users.link-child');
        Route::delete('/users/{user}/unlink-child', [AdminUserController::class, 'unlinkChild'])->name('users.unlink-child');

        // Academic Years
        Route::get('/academic-years', [AcademicYearWebController::class, 'index'])->name('academic-years.index');
        Route::get('/academic-years/create', [AcademicYearWebController::class, 'create'])->name('academic-years.create');
        Route::get('/academic-years/{year}', [AcademicYearWebController::class, 'show'])->name('academic-years.show');
        Route::post('/academic-years', [AcademicYearWebController::class, 'store'])->name('academic-years.store');

        // Calendar
        Route::get('/calendar', [CalendarWebController::class, 'index'])->name('calendar.index');
        Route::get('/calendar/create', [CalendarWebController::class, 'create'])->name('calendar.create');
        Route::get('/calendar/{event}', [CalendarWebController::class, 'show'])->name('calendar.show');
        Route::post('/calendar', [CalendarWebController::class, 'store'])->name('calendar.store');

        // Schedule builder
        Route::get('/schedule', [ScheduleWebController::class, 'index'])->name('schedule.index');
        Route::post('/schedule', [ScheduleWebController::class, 'store'])->name('schedule.store');

        // Subjects
        Route::get('/subjects', [SubjectWebController::class, 'index'])->name('subjects.index');
        Route::get('/subjects/create', [SubjectWebController::class, 'create'])->name('subjects.create');
        Route::post('/subjects', [SubjectWebController::class, 'store'])->name('subjects.store');
        Route::get('/subjects/{subject}', [SubjectWebController::class, 'show'])->name('subjects.show');
        Route::get('/subjects/{subject}/edit', [SubjectWebController::class, 'edit'])->name('subjects.edit');
        Route::put('/subjects/{subject}', [SubjectWebController::class, 'update'])->name('subjects.update');
        Route::delete('/subjects/{subject}', [SubjectWebController::class, 'destroy'])->name('subjects.destroy');

        // Settings
        Route::get('/settings', [SettingsWebController::class, 'index'])->name('settings.index');

        // Teacher Assignments
        Route::get('/assignments', [AssignmentWebController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/create', [AssignmentWebController::class, 'create'])->name('assignments.create');
        Route::post('/assignments', [AssignmentWebController::class, 'store'])->name('assignments.store');
    });

    // Teacher routes
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/schedule', [TeacherWebController::class, 'schedule'])->name('schedule');

        // Attendance — justifications must be declared before any future param routes
        Route::get('/attendance/justifications', [TeacherAttendanceController::class, 'justifications'])->name('justifications');
        Route::post('/attendance/justifications/{justification}/approve', [TeacherAttendanceController::class, 'approveJustification'])->name('justifications.approve');
        Route::post('/attendance/justifications/{justification}/reject', [TeacherAttendanceController::class, 'rejectJustification'])->name('justifications.reject');
        Route::get('/attendance', [TeacherAttendanceController::class, 'index'])->name('attendance');
        Route::post('/attendance', [TeacherAttendanceController::class, 'store'])->name('attendance.store');

        // Behavioral notes
        Route::get('/behavioral-notes', [TeacherBehavioralNoteController::class, 'index'])->name('behavioral-notes');
        Route::post('/behavioral-notes', [TeacherBehavioralNoteController::class, 'store'])->name('behavioral-notes.store');
    });

    // Student pages
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/schedule', [StudentWebController::class, 'schedule'])->name('schedule');
        Route::get('/grades', [StudentWebController::class, 'grades'])->name('grades');
        Route::get('/attendance', [StudentWebController::class, 'attendance'])->name('attendance');
    });

    // Parent pages
    Route::middleware('role:parent')->prefix('parent')->name('parent.')->group(function () {
        Route::get('/children', [ParentWebController::class, 'children'])->name('children');
        Route::get('/children/{child}/schedule', [ParentWebController::class, 'childSchedule'])->name('child-schedule');
        Route::get('/grades', [ParentWebController::class, 'grades'])->name('grades');
        Route::get('/attendance', [ParentWebController::class, 'attendance'])->name('attendance');
        Route::post('/attendance/{attendance}/justify', [ParentWebController::class, 'storeJustification'])->name('attendance.justify');
        Route::get('/behavioral-notes', [ParentWebController::class, 'behavioralNotes'])->name('behavioral-notes');
    });

    // Classrooms (accessible to admin and teacher)
    Route::middleware('role:admin,teacher')->prefix('classrooms')->name('classrooms.')->group(function () {
        Route::get('/', [ClassroomWebController::class, 'index'])->name('index');
        Route::get('/{classroom}', [ClassroomWebController::class, 'show'])->name('show');
    });
});
