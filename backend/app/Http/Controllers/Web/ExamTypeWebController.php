<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamTypeWebController extends Controller
{
    public function index(): View
    {
        $examTypes = ExamType::with('semester.academicYear')->orderByDesc('id')->paginate(20);
        $semesters = Semester::with('academicYear')->orderByDesc('id')->get();

        return view('admin.exam-types.index', compact('examTypes', 'semesters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'weight_percent' => 'required|numeric|min:0.01|max:100',
            'semester_id'    => 'required|integer|exists:semesters,id',
        ]);

        ExamType::create($request->only('name', 'weight_percent', 'semester_id'));

        return redirect()->route('admin.exam-types.index')->with('success', 'Exam type created.');
    }

    public function update(Request $request, ExamType $examType): RedirectResponse
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'weight_percent' => 'required|numeric|min:0.01|max:100',
        ]);

        $examType->update($request->only('name', 'weight_percent'));

        return redirect()->route('admin.exam-types.index')->with('success', 'Exam type updated.');
    }

    public function destroy(ExamType $examType): RedirectResponse
    {
        $examType->delete();

        return redirect()->route('admin.exam-types.index')->with('success', 'Exam type deleted.');
    }
}
