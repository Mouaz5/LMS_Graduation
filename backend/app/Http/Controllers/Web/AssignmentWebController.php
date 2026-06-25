<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreAssignmentRequest;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\TeacherSubjectClassroom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssignmentWebController extends Controller
{
    public function index(): View
    {
        $assignments = TeacherSubjectClassroom::with(['teacher', 'subject', 'classroom.grade', 'academicYear'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.assignments.index', compact('assignments'));
    }

    public function create(): View
    {
        $teachers = User::where('role', 'teacher')->orderBy('name')->get(['id', 'name']);
        $subjects = Subject::orderBy('name')->get(['id', 'name', 'code']);
        $classrooms = Classroom::with('grade')->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get(['id', 'name']);

        return view('admin.assignments.create', compact('teachers', 'subjects', 'classrooms', 'academicYears'));
    }

    public function store(StoreAssignmentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Prevent duplicate assignment
        $exists = TeacherSubjectClassroom::where('teacher_user_id', $validated['teacher_user_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('classroom_id', $validated['classroom_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['duplicate' => __('This teacher is already assigned to this subject in this classroom.')])->withInput();
        }

        TeacherSubjectClassroom::create($validated);

        return redirect()->route('admin.assignments.index')->with('success', __('Teacher assignment created successfully.'));
    }
}
