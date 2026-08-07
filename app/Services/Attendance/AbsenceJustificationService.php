<?php

namespace App\Services\Attendance;

use App\Enums\AbsenceJustificationStatus;
use App\Models\AbsenceJustification;
use App\Models\Attendance;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\Parent\ParentAccessService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AbsenceJustificationService
{
    public function __construct(
        private ParentAccessService $access,
        private AttendanceService $attendance,
    ) {}

    public function submit(
        User $parent,
        Attendance $attendance,
        string $reason,
        ?UploadedFile $document,
    ): AbsenceJustification {
        $this->access->assertStudentBelongsToParent(
            $parent,
            $attendance->student_user_id,
            __('You are not a parent of this student.'),
        );

        if ($attendance->justification()->exists()) {
            throw new HttpException(422, __('A justification already exists for this absence.'));
        }

        $documentUrl = null;
        if ($document) {
            $path = $document->store('justifications', 'public');
            $documentUrl = Storage::url($path);
        }

        return AbsenceJustification::create([
            'attendance_id' => $attendance->id,
            'reason' => $reason,
            'submitted_by' => $parent->id,
            'document_url' => $documentUrl,
            'status' => AbsenceJustificationStatus::PENDING,
        ]);
    }

    public function approve(User $teacher, AbsenceJustification $justification): void
    {
        $justification->loadMissing('attendance');
        $this->attendance->assertTeacherCanRecord(
            $teacher,
            $justification->attendance->classroom_id,
            null,
        );
        $this->attendance->approveJustification($justification);
    }

    public function reject(User $teacher, AbsenceJustification $justification): void
    {
        $justification->loadMissing('attendance');
        $this->attendance->assertTeacherCanRecord(
            $teacher,
            $justification->attendance->classroom_id,
            null,
        );
        $this->attendance->rejectJustification($justification);
    }
}
