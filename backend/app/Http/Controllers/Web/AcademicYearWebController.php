<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreAcademicYearWebRequest;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
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

    public function store(StoreAcademicYearWebRequest $request): RedirectResponse
    {
        $school = \App\Models\School::first();

        AcademicYear::create([
            'school_id' => $school->id,
            'name'       => $request->name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'is_active'  => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.academic-years.index')->with('success', __('Academic year created successfully.'));
    }
}
