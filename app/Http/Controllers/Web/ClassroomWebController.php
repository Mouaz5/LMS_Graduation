<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\View\View;

class ClassroomWebController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        if ($user->role === 'teacher') {
            $classroomIds = $user->teacherAssignments()->pluck('classroom_id')->unique()->toArray();
            $classrooms = Classroom::with(['grade', 'studentProfiles.student', 'teacherAssignments.subject'])
                ->whereIn('id', $classroomIds)
                ->get();
        } else {
            $classrooms = Classroom::with(['grade', 'studentProfiles.student', 'teacherAssignments.subject'])
                ->get();
        }

        return view('admin.classrooms.index', compact('classrooms'));
    }

    public function show(Classroom $classroom): View
    {
        $classroom->load(['grade', 'studentProfiles.student', 'teacherAssignments.subject', 'teacherAssignments.teacher', 'teacherAssignments.academicYear']);
        return view('admin.classrooms.show', compact('classroom'));
    }
}
