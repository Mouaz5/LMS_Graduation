<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParentStudentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'parent_user_id' => 'required|exists:users,id',
            'student_user_id' => 'required|exists:users,id',
            'relation' => 'required|in:father,mother,guardian',
        ]);

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

        return response()->json([
            'message' => 'Student linked to parent successfully.',
            'parent' => $parent->only('id', 'name', 'email'),
            'student' => $student->only('id', 'name', 'email'),
            'relation' => $validated['relation'],
        ], 201);
    }
}
