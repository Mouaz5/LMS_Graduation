<?php

use App\Http\Controllers\Web\AcademicYearWebController;
use App\Http\Controllers\Web\AdminUserController;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\CalendarWebController;
use App\Http\Controllers\Web\ClassroomWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ScheduleWebController;
use App\Http\Controllers\Web\SettingsWebController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

// Password reset
Route::get('/forgot-password', [AuthWebController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthWebController::class, 'sendResetLink'])->name('password.email');

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

        // Settings
        Route::get('/settings', [SettingsWebController::class, 'index'])->name('settings.index');
    });

    // Classrooms (accessible to admin and teacher)
    Route::middleware('role:admin,teacher')->prefix('classrooms')->name('classrooms.')->group(function () {
        Route::get('/', [ClassroomWebController::class, 'index'])->name('index');
        Route::get('/{classroom}', [ClassroomWebController::class, 'show'])->name('show');
    });
});
