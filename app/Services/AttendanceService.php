<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AbsenceJustification;
use App\Models\ScheduleSlot;
use App\Models\StudentProfile;
use App\Models\TeacherSubjectClassroom;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;

class AttendanceService
{
    /**
     * Assert that the given teacher is authorized to record attendance for a classroom.
     * Throws 403 if unauthorized.
     */
    public function assertTeacherCanRecord(User $teacher, int $classroomId, ?int $scheduleSlotId): void
    {
        if ($scheduleSlotId !== null) {
            $slot = ScheduleSlot::find($scheduleSlotId);
            $authorized = $slot
                && $slot->teacher_user_id === $teacher->id
                && $slot->classroom_id === $classroomId;
        } else {
            $authorized = TeacherSubjectClassroom::where('teacher_user_id', $teacher->id)
                ->where('classroom_id', $classroomId)
                ->exists();
        }

        if (! $authorized) {
            throw new HttpResponseException(
                response()->json(['message' => 'You are not assigned to this classroom.'], 403)
            );
        }
    }

    /**
     * Record bulk attendance. Uses updateOrCreate so resubmitting the same day
     * updates existing records instead of creating duplicates.
     *
     * @param  array $entries  [['student_id' => int, 'status' => string], ...]
     */
    public function recordBulk(
        User $teacher,
        int $classroomId,
        string $date,
        array $entries,
        ?int $scheduleSlotId
    ): Collection {
        $this->assertTeacherCanRecord($teacher, $classroomId, $scheduleSlotId);

        $records = collect();
        foreach ($entries as $entry) {
            $record = Attendance::updateOrCreate(
                [
                    'student_user_id' => $entry['student_id'],
                    'classroom_id'    => $classroomId,
                    'date'            => $date,
                ],
                [
                    'status'           => $entry['status'],
                    'schedule_slot_id' => $scheduleSlotId,
                    'recorded_by'      => $teacher->id,
                ]
            );
            $records->push($record);
        }

        return $records;
    }

    /**
     * Approve a justification and cascade attendance status to 'excused'.
     */
    public function approveJustification(AbsenceJustification $justification): void
    {
        $justification->update(['status' => 'approved']);
        $justification->attendance->update(['status' => 'excused']);
    }

    /**
     * Reject a justification without changing attendance status.
     */
    public function rejectJustification(AbsenceJustification $justification): void
    {
        $justification->update(['status' => 'rejected']);
    }

    /**
     * Get students enrolled in a classroom for the attendance form.
     */
    public function getClassroomStudents(int $classroomId): Collection
    {
        return StudentProfile::where('classroom_id', $classroomId)
            ->with('student')
            ->get();
    }
}
