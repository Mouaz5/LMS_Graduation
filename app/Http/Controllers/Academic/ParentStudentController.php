<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreParentStudentRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ParentStudentController extends Controller
{
    public function store(StoreParentStudentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $parent = User::findOrFail($validated['parent_user_id']);
        $student = User::findOrFail($validated['student_user_id']);

        if ($parent->role !== 'parent') {
            return response()->json(['message' => 'The specified user is not a parent.'], 422);
        }

        if ($student->role !== 'student') {
            return response()->json(['message' => 'The specified user is not a student.'], 422);
        }

        $exists = DB::table('parent_student')
            ->where('parent_user_id', $validated['parent_user_id'])
            ->where('student_user_id', $validated['student_user_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'This link already exists.'], 422);
        }

        DB::table('parent_student')->insert([
            'parent_user_id' => $validated['parent_user_id'],
            'student_user_id' => $validated['student_user_id'],
            'relation' => $validated['relation'],
        ]);

        return ApiResponse::success(
            data: [
                'parent' => $parent->only('id', 'name', 'email'),
                'student' => $student->only('id', 'name', 'email'),
                'relation' => $validated['relation'],
            ],
            message: 'Student linked to parent successfully.',
            status: 201,
        );
    }
}
