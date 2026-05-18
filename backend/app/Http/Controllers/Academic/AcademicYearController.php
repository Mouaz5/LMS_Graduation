<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index(): JsonResponse
    {
        $years = AcademicYear::with('semesters')->orderBy('start_date', 'desc')->get();
        return response()->json($years);
    }

    public function show(int $id): JsonResponse
    {
        $year = AcademicYear::with('semesters')->findOrFail($id);
        return response()->json($year);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $year = AcademicYear::create($validated);

        return response()->json($year, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $year = AcademicYear::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $year->update($validated);

        return response()->json($year);
    }

    public function destroy(int $id): JsonResponse
    {
        $year = AcademicYear::findOrFail($id);
        $year->delete();

        return response()->json(['message' => 'Academic year deleted.']);
    }
}
