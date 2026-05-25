<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectWebController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::with('school')->orderBy('name')->get();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        $schools = School::orderBy('name')->get();

        return view('admin.subjects.create', compact('schools'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:20|unique:subjects,code',
        ]);

        Subject::create($request->only('school_id', 'name', 'code'));

        return redirect()->route('admin.subjects.index')
                         ->with('success', 'Subject created successfully.');
    }

    public function show(Subject $subject): View
    {
        $subject->load([
            'school',
            'teacherAssignments.teacher',
            'teacherAssignments.classroom.grade',
            'teacherAssignments.academicYear',
        ]);

        $teacherCount   = $subject->teacherAssignments->unique('teacher_user_id')->count();
        $classroomCount = $subject->teacherAssignments->unique('classroom_id')->count();

        return view('admin.subjects.show', compact('subject', 'teacherCount', 'classroomCount'));
    }

    public function edit(Subject $subject): View
    {
        $schools = School::orderBy('name')->get();

        return view('admin.subjects.edit', compact('subject', 'schools'));
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects,code,' . $subject->id,
        ]);

        $subject->update($request->only('name', 'code'));

        return redirect()->route('admin.subjects.index')
                         ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')
                         ->with('success', 'Subject deleted.');
    }
}
