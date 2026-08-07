<?php

namespace App\Http\Controllers\Academic;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreTeacherAssignmentRequest;
use App\Http\Responses\ApiResponse;
use App\Models\TeacherSubjectClassroom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherAssignmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === UserRole::TEACHER) {
            $assignments = TeacherSubjectClassroom::with(['subject', 'classroom.grade', 'academicYear'])
                ->where('teacher_user_id', $user->id)
                ->get();
        } else {
            $assignments = TeacherSubjectClassroom::with(['teacher', 'subject', 'classroom.grade', 'academicYear'])
                ->get();
        }

        return ApiResponse::success(data: $assignments);
    }

    public function store(StoreTeacherAssignmentRequest $request): JsonResponse
    {
        $assignment = TeacherSubjectClassroom::firstOrCreate($request->validated());

        return ApiResponse::success(data: $assignment->load(['subject', 'classroom.grade', 'academicYear']), status: 201);
    }
}
