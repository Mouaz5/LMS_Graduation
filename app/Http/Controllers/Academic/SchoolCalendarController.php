<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreSchoolCalendarRequest;
use App\Http\Responses\ApiResponse;
use App\Models\SchoolCalendar;
use Illuminate\Http\JsonResponse;

class SchoolCalendarController extends Controller
{
    public function index(): JsonResponse
    {
        $events = SchoolCalendar::orderBy('date')->get();

        return ApiResponse::success(data: $events);
    }

    public function store(StoreSchoolCalendarRequest $request): JsonResponse
    {
        $event = SchoolCalendar::create($request->validated());

        return ApiResponse::success(data: $event, status: 201);
    }
}
