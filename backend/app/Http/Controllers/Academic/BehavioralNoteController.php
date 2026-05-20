<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\BehavioralNote;
use App\Models\TeacherSubjectClassroom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BehavioralNoteController extends Controller
{
    /**
     * POST /api/v1/behavioral-notes
     * Role: teacher only.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_user_id' => 'required|exists:users,id',
            'note'            => 'required|string|max:2000',
            'severity'        => 'required|in:info,warning,critical',
            'date'            => 'required|date|date_format:Y-m-d',
        ]);

        abort_unless(
            TeacherSubjectClassroom::where('teacher_user_id', $request->user()->id)
                ->whereHas('classroom.studentProfiles', fn ($q) => $q->where('user_id', $validated['student_user_id']))
                ->exists(),
            403,
            'You are not a teacher of this student.'
        );

        $note = BehavioralNote::create([
            ...$validated,
            'teacher_user_id' => $request->user()->id,
        ]);

        return response()->json($note->load(['student', 'teacher']), 201);
    }

    /**
     * GET /api/v1/behavioral-notes?student_id=X
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        $user      = $request->user();
        $studentId = $request->integer('student_id');

        match ($user->role) {
            'teacher' => abort_unless(
                TeacherSubjectClassroom::where('teacher_user_id', $user->id)
                    ->whereHas('classroom.studentProfiles', fn ($q) => $q->where('user_id', $studentId))
                    ->exists(),
                403, 'Student not in your classrooms.'
            ),
            'parent'  => abort_unless(
                $user->children()->where('student_user_id', $studentId)->exists(),
                403, 'This student is not your child.'
            ),
            default => null,
        };

        $notes = BehavioralNote::where('student_user_id', $studentId)
            ->with(['teacher', 'student'])
            ->orderByDesc('date')
            ->paginate(20);

        return response()->json($notes);
    }
}
