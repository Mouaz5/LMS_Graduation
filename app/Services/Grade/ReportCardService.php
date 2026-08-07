<?php

namespace App\Services\Grade;

use App\Domain\ReportCardData;
use App\Models\Semester;
use App\Models\User;
use App\Services\ReportCardAssembler;
use Illuminate\Support\Collection;

class ReportCardService
{
    public function __construct(private ReportCardAssembler $assembler) {}

    public function semesters(): Collection
    {
        return Semester::with('academicYear')->orderByDesc('id')->get();
    }

    public function studentResults(User $student, ?int $semesterId): array
    {
        $semesters = $this->semesters();
        $selectedSemesterId = $semesterId ?: $semesters->first()?->id;

        if (! $selectedSemesterId) {
            return [
                'user' => $student,
                'semesters' => $semesters,
                'selectedSemesterId' => null,
                'summaries' => collect(),
                'grades' => collect(),
                'examTypes' => collect(),
            ];
        }

        $data = $this->assembler->assembleStudent($student, $selectedSemesterId);

        return [
            'user' => $student,
            'semesters' => $semesters,
            'selectedSemesterId' => $selectedSemesterId,
            'summaries' => $data->summaries,
            'grades' => $data->grades->groupBy('subject_id'),
            'examTypes' => $data->examTypes,
        ];
    }

    public function assemble(User $student, ?int $semesterId): ReportCardData
    {
        return $this->assembler->assembleStudent($student, $semesterId);
    }

    public function assembleById(int $studentId, ?int $semesterId): ReportCardData
    {
        return $this->assembler->assemble($studentId, $semesterId);
    }
}
