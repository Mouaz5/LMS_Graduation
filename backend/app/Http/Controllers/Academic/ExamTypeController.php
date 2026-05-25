<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ExamType::with('semester.academicYear');

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        return response()->json($query->orderBy('semester_id')->orderBy('id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'weight_percent' => 'required|numeric|min:0.01|max:100',
            'semester_id'    => 'required|integer|exists:semesters,id',
        ]);

        $examType = ExamType::create($validated);

        return response()->json($examType->load('semester'), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $examType  = ExamType::findOrFail($id);
        $validated = $request->validate([
            'name'           => 'sometimes|string|max:100',
            'weight_percent' => 'sometimes|numeric|min:0.01|max:100',
        ]);

        $examType->update($validated);

        return response()->json($examType->load('semester'));
    }

    public function destroy(int $id): JsonResponse
    {
        ExamType::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted.']);
    }
}
