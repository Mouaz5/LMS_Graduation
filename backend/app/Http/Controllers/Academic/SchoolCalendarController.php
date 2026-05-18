<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\SchoolCalendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolCalendarController extends Controller
{
    public function index(): JsonResponse
    {
        $events = SchoolCalendar::orderBy('date')->get();
        return response()->json($events);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'date' => 'required|date',
            'type' => 'required|in:holiday,event,exam',
            'description' => 'required|string|max:500',
        ]);

        $event = SchoolCalendar::create($validated);

        return response()->json($event, 201);
    }
}
