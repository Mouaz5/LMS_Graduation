<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\TeacherSubjectClassroom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherAssignmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'teacher') {
            $assignments = TeacherSubjectClassroom::with(['subject', 'classroom.grade', 'academicYear'])
                ->where('teacher_user_id', $user->id)
                ->get();
        } else {
            $assignments = TeacherSubjectClassroom::with(['teacher', 'subject', 'classroom.grade', 'academicYear'])
                ->get();
        }

        return response()->json($assignments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'teacher_user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $assignment = TeacherSubjectClassroom::firstOrCreate($validated);

        return response()->json($assignment->load(['subject', 'classroom.grade', 'academicYear']), 201);
    }
}
