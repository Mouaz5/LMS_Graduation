<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreAcademicYearRequest;
use App\Http\Requests\Academic\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;

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

    public function store(StoreAcademicYearRequest $request): JsonResponse
    {
        $year = AcademicYear::create($request->validated());

        return response()->json($year, 201);
    }

    public function update(UpdateAcademicYearRequest $request, int $id): JsonResponse
    {
        $year = AcademicYear::findOrFail($id);

        $year->update($request->validated());

        return response()->json($year);
    }

    public function destroy(int $id): JsonResponse
    {
        $year = AcademicYear::findOrFail($id);
        $year->delete();

        return response()->json(['message' => 'Academic year deleted.']);
    }
}
