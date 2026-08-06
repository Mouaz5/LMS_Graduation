<?php

namespace App\Services;

use App\Domain\ReportCardData;
use App\Models\ExamType;
use App\Models\GradeSummary;
use App\Models\Semester;
use App\Models\StudentGrade;
use App\Models\User;
use Illuminate\Support\Collection;

class ReportCardAssembler
{
    public function assemble(int $studentId, ?int $semesterId): ReportCardData
    {
        $student = User::with('studentProfile.classroom.grade')->findOrFail($studentId);

        $semester = $semesterId
            ? Semester::with('academicYear')->findOrFail($semesterId)
            : null;

        $summaries = GradeSummary::where('student_user_id', $studentId)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->with(['subject', 'semester.academicYear'])
            ->get();

        $grades = StudentGrade::where('student_user_id', $studentId)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->with(['subject', 'examType'])
            ->get();

        $examTypes = $semesterId
            ? ExamType::where('semester_id', $semesterId)->orderBy('id')->get()
            : collect();

        return new ReportCardData($student, $semester, $summaries, $grades, $examTypes);
    }
}
