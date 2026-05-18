<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicYearWebController extends Controller
{
    public function index(): View
    {
        $years = AcademicYear::withCount('semesters')->orderBy('start_date', 'desc')->get();
        return view('admin.academic-years.index', compact('years'));
    }

    public function show(AcademicYear $year): View
    {
        $year->load(['semesters', 'school']);
        return view('admin.academic-years.show', compact('year'));
    }

    public function create(): View
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $school = \App\Models\School::first();

        AcademicYear::create([
            'school_id' => $school->id,
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.academic-years.index')->with('success', 'Academic year created successfully.');
    }
}
