<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Subject::with('school')->orderBy('name')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:20|unique:subjects,code',
        ]);

        $subject = Subject::create($validated);

        return response()->json($subject->load('school'), 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(Subject::with('school')->findOrFail($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:20|unique:subjects,code,' . $id,
        ]);

        $subject->update($validated);

        return response()->json($subject->load('school'));
    }

    public function destroy(int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return response()->json(['message' => 'Subject deleted.']);
    }
}
