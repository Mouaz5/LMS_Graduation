<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index(): JsonResponse
    {
        $semesters = Semester::with('academicYear')->orderBy('start_date', 'desc')->get();
        return response()->json($semesters);
    }

    public function show(int $id): JsonResponse
    {
        $semester = Semester::with('academicYear')->findOrFail($id);
        return response()->json($semester);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $semester = Semester::create($validated);

        return response()->json($semester, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $semester = Semester::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $semester->update($validated);

        return response()->json($semester);
    }

    public function destroy(int $id): JsonResponse
    {
        $semester = Semester::findOrFail($id);
        $semester->delete();

        return response()->json(['message' => 'Semester deleted.']);
    }
}
