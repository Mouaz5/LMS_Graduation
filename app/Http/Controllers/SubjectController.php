<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subject\StoreSubjectRequest;
use App\Http\Requests\Subject\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;

class SubjectController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Subject::with('school')->orderBy('name')->get());
    }

    public function store(StoreSubjectRequest $request): JsonResponse
    {
        $subject = Subject::create($request->validated());

        return response()->json($subject->load('school'), 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(Subject::with('school')->findOrFail($id));
    }

    public function update(UpdateSubjectRequest $request, int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);

        $subject->update($request->validated());

        return response()->json($subject->load('school'));
    }

    public function destroy(int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return response()->json(['message' => 'Subject deleted.']);
    }
}
