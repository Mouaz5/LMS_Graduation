<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\ScheduleSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ScheduleSlotController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'semester_id'  => 'required|exists:semesters,id',
        ]);

        $slots = ScheduleSlot::with(['subject', 'teacher', 'classroom.grade'])
            ->where('classroom_id', $request->classroom_id)
            ->where('semester_id', $request->semester_id)
            ->orderBy('day_of_week')
            ->orderBy('period_number')
            ->get();

        return response()->json($slots);
    }

    public function mySchedule(Request $request): JsonResponse
    {
        $slots = ScheduleSlot::with(['subject', 'classroom.grade', 'semester'])
            ->where('teacher_user_id', $request->user()->id)
            ->orderBy('day_of_week')
            ->orderBy('period_number')
            ->get();

        return response()->json($slots);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'classroom_id'    => 'required|exists:classrooms,id',
            'subject_id'      => 'required|exists:subjects,id',
            'teacher_user_id' => 'required|exists:users,id',
            'day_of_week'     => 'required|in:sunday,monday,tuesday,wednesday,thursday',
            'period_number'   => 'required|integer|min:1|max:8',
            'start_time'      => 'required|date_format:H:i',
            'end_time'        => 'required|date_format:H:i|after:start_time',
            'semester_id'     => 'required|exists:semesters,id',
        ]);

        $conflict = ScheduleSlot::where('teacher_user_id', $validated['teacher_user_id'])
            ->where('semester_id', $validated['semester_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('period_number', $validated['period_number'])
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'period_number' => 'This teacher already has a slot in period '
                    . $validated['period_number'] . ' on '
                    . $validated['day_of_week'] . ' this semester.',
            ]);
        }

        $slot = ScheduleSlot::create($validated);

        return response()->json(
            $slot->load(['subject', 'teacher', 'classroom.grade', 'semester']),
            201
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $slot = ScheduleSlot::findOrFail($id);

        $validated = $request->validate([
            'classroom_id'    => 'sometimes|exists:classrooms,id',
            'subject_id'      => 'sometimes|exists:subjects,id',
            'teacher_user_id' => 'sometimes|exists:users,id',
            'day_of_week'     => 'sometimes|in:sunday,monday,tuesday,wednesday,thursday',
            'period_number'   => 'sometimes|integer|min:1|max:8',
            'start_time'      => 'sometimes|date_format:H:i',
            'end_time'        => 'sometimes|date_format:H:i|after:start_time',
            'semester_id'     => 'sometimes|exists:semesters,id',
        ]);

        $teacherId  = $validated['teacher_user_id'] ?? $slot->teacher_user_id;
        $semesterId = $validated['semester_id']      ?? $slot->semester_id;
        $day        = $validated['day_of_week']      ?? $slot->day_of_week;
        $period     = $validated['period_number']    ?? $slot->period_number;

        $conflict = ScheduleSlot::where('teacher_user_id', $teacherId)
            ->where('semester_id', $semesterId)
            ->where('day_of_week', $day)
            ->where('period_number', $period)
            ->where('id', '!=', $slot->id)
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'period_number' => 'This teacher already has a slot in that period.',
            ]);
        }

        $slot->update($validated);

        return response()->json($slot->load(['subject', 'teacher', 'classroom.grade', 'semester']));
    }

    public function destroy(int $id): JsonResponse
    {
        ScheduleSlot::findOrFail($id)->delete();

        return response()->json(['message' => 'Schedule slot deleted.']);
    }
}
