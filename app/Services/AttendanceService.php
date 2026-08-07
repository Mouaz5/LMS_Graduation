<?php

namespace App\Services;

use App\Enums\AbsenceJustificationStatus;
use App\Enums\AttendanceStatus;
use App\Models\AbsenceJustification;
use App\Models\Attendance;
use App\Models\ScheduleSlot;
use App\Models\StudentProfile;
use App\Models\TeacherSubjectClassroom;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Assert that the given teacher is authorized to record attendance for a classroom.
     * Throws 403 if unauthorized.
     */
    public function teacherCanRecord(User $teacher, int $classroomId, ?int $scheduleSlotId): bool
    {
        if ($scheduleSlotId !== null) {
            $slot = ScheduleSlot::find($scheduleSlotId);

            return (bool) ($slot
                && $slot->teacher_user_id === $teacher->id
                && $slot->classroom_id === $classroomId);
        }

        return TeacherSubjectClassroom::where('teacher_user_id', $teacher->id)
            ->where('classroom_id', $classroomId)
            ->exists();
    }

    public function assertTeacherCanRecord(User $teacher, int $classroomId, ?int $scheduleSlotId): void
    {
        if (! $this->teacherCanRecord($teacher, $classroomId, $scheduleSlotId)) {
            throw new HttpResponseException(
                response()->json(['message' => 'You are not assigned to this classroom.'], 403)
            );
        }
    }

    /**
     * Record bulk attendance. Uses updateOrCreate so resubmitting the same day
     * updates existing records instead of creating duplicates.
     *
     * @param  array  $entries  [['student_id' => int, 'status' => string], ...]
     */
    public function recordBulk(
        User $teacher,
        int $classroomId,
        string $date,
        array $entries,
        ?int $scheduleSlotId
    ): Collection {
        $this->assertTeacherCanRecord($teacher, $classroomId, $scheduleSlotId);

        $studentIds = collect($entries)->pluck('student_id')->unique();
        $enrolledStudentIds = StudentProfile::where('classroom_id', $classroomId)
            ->whereIn('user_id', $studentIds)
            ->pluck('user_id');

        if ($enrolledStudentIds->count() !== $studentIds->count()) {
            throw new HttpResponseException(
                response()->json(['message' => 'One or more students are not enrolled in this classroom.'], 403)
            );
        }

        $records = collect();
        foreach ($entries as $entry) {
            $record = Attendance::updateOrCreate(
                [
                    'student_user_id' => $entry['student_id'],
                    'classroom_id' => $classroomId,
                    'date' => $date,
                ],
                [
                    'status' => AttendanceStatus::from($entry['status']),
                    'schedule_slot_id' => $scheduleSlotId,
                    'recorded_by' => $teacher->id,
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
        DB::transaction(function () use ($justification): void {
            $justification->update(['status' => AbsenceJustificationStatus::APPROVED]);
            $justification->attendance->update(['status' => AttendanceStatus::EXCUSED]);
        });
    }

    /**
     * Reject a justification without changing attendance status.
     */
    public function rejectJustification(AbsenceJustification $justification): void
    {
        $justification->update(['status' => AbsenceJustificationStatus::REJECTED]);
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
