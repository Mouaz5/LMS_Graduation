<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'teacher') {
            // Teachers see only classrooms they're assigned to
            $classroomIds = $user->teacherAssignments()
                ->pluck('classroom_id')
                ->unique()
                ->toArray();

            $classrooms = Classroom::with(['grade', 'teacherAssignments.subject', 'studentProfiles.student'])
                ->whereIn('id', $classroomIds)
                ->get();
        } else {
            $classrooms = Classroom::with(['grade', 'studentProfiles.student'])->get();
        }

        return response()->json($classrooms);
    }
}
