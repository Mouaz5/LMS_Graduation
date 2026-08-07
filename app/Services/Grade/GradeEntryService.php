<?php

namespace App\Services\Grade;

use App\Enums\UserRole;
use App\Models\ExamType;
use App\Models\StudentGrade;
use App\Models\TeacherSubjectClassroom;
use App\Models\User;
use App\Services\GradeService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GradeEntryService
{
    public function storeBulk(User $actor, array $rows): int
    {
        foreach ($rows as $index => $row) {
            if ((float) $row['score'] > (float) $row['max_score']) {
                throw ValidationException::withMessages([
                    "grades.$index.score" => 'Score cannot exceed max_score.',
                ]);
            }

            $this->assertCanEnterGrade(
                $actor,
                (int) $row['subject_id'],
                (int) $row['student_id'],
            );
        }

        DB::transaction(function () use ($actor, $rows): void {
            $tuples = [];

            foreach ($rows as $row) {
                $examType = ExamType::findOrFail($row['exam_type_id']);

                StudentGrade::updateOrCreate(
                    [
                        'student_user_id' => $row['student_id'],
                        'subject_id' => $row['subject_id'],
                        'exam_type_id' => $row['exam_type_id'],
                    ],
                    [
                        'semester_id' => $examType->semester_id,
                        'teacher_user_id' => $actor->id,
                        'score' => $row['score'],
                        'max_score' => $row['max_score'],
                    ],
                );

                $tuples[] = [
                    'student_user_id' => $row['student_id'],
                    'subject_id' => $row['subject_id'],
                    'semester_id' => $examType->semester_id,
                ];
            }

            GradeService::refreshSummaries($tuples);
        });

        return count($rows);
    }

    public function storeWeb(
        User $teacher,
        int $subjectId,
        int $examTypeId,
        float $maxScore,
        array $scores,
    ): int {
        $rows = [];

        foreach ($scores as $studentId => $score) {
            if ($score === null || $score === '') {
                continue;
            }

            $rows[] = [
                'student_id' => (int) $studentId,
                'subject_id' => $subjectId,
                'exam_type_id' => $examTypeId,
                'score' => $score,
                'max_score' => $maxScore,
            ];
        }

        return $this->storeBulk($teacher, $rows);
    }

    private function assertCanEnterGrade(User $actor, int $subjectId, int $studentId): void
    {
        if ($actor->role === UserRole::ADMIN) {
            return;
        }

        $isAssigned = TeacherSubjectClassroom::where('teacher_user_id', $actor->id)
            ->where('subject_id', $subjectId)
            ->whereHas('classroom.studentProfiles', fn ($query) => $query->where('user_id', $studentId))
            ->exists();

        if (! $isAssigned) {
            throw new AuthorizationException(__('You are not assigned to this student and subject.'));
        }
    }
}
