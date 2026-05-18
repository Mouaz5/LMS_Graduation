<?php

use App\Http\Controllers\Academic\AcademicYearController;
use App\Http\Controllers\Academic\ClassroomController;
use App\Http\Controllers\Academic\ParentStudentController;
use App\Http\Controllers\Academic\SchoolCalendarController;
use App\Http\Controllers\Academic\SemesterController;
use App\Http\Controllers\Academic\TeacherAssignmentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('password/reset', [AuthController::class, 'resetPassword']);
});

// Protected auth routes
Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('register', [AuthController::class, 'register'])->middleware('role:admin');
});

// User management (admin only)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}/role', [UserController::class, 'updateRole']);
    Route::patch('users/{id}/status', [UserController::class, 'updateStatus']);
});

// Roles & permissions
Route::middleware('auth:sanctum')->group(function () {
    Route::get('roles', [RoleController::class, 'index']);
});

// Academic Years (admin only)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('academic-years', AcademicYearController::class);
    Route::apiResource('semesters', SemesterController::class);
    Route::post('parent-student', [ParentStudentController::class, 'store']);
    Route::post('teacher-assignments', [TeacherAssignmentController::class, 'store']);
    Route::post('school-calendar', [SchoolCalendarController::class, 'store']);
});

// Classrooms (all authenticated users, filtered by role in controller)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('classrooms', [ClassroomController::class, 'index']);
    Route::get('teacher-assignments', [TeacherAssignmentController::class, 'index']);
    Route::get('school-calendar', [SchoolCalendarController::class, 'index']);
});
