<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Grade\BulkStoreGradeRequest;
use App\Http\Requests\Grade\ClassAverageRequest;
use App\Models\ExamType;
use App\Models\GradeSummary;
use App\Models\StudentGrade;
use App\Models\TeacherSubjectClassroom;
use App\Models\User;
use App\Services\GradeService;
use App\Services\ReportCardAssembler;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GradeController extends Controller
{
    public function __construct(private ReportCardAssembler $assembler) {}

    /** POST /api/v1/grades/bulk */
    public function bulkStore(BulkStoreGradeRequest $request): JsonResponse
    {
        $teacher = Auth::user();

        $validated = $request->validated();

        // Validate score <= max_score per row
        foreach ($validated['grades'] as $i => $row) {
            if ($row['score'] > $row['max_score']) {
                throw ValidationException::withMessages([
                    "grades.$i.score" => 'Score cannot exceed max_score.',
                ]);
            }
        }

        // Verify teacher is assigned to each subject (skip for admin)
        if ($teacher->role !== 'admin') {
            $subjectIds = collect($validated['grades'])->pluck('subject_id')->unique();
            $assignedSubjects = TeacherSubjectClassroom::where('teacher_user_id', $teacher->id)
                ->whereIn('subject_id', $subjectIds)
                ->pluck('subject_id')
                ->unique();

            foreach ($subjectIds as $sid) {
                if (!$assignedSubjects->contains($sid)) {
                    return response()->json(['message' => "Not assigned to subject #$sid."], 403);
                }
            }
        }

        $tuples = [];

        DB::transaction(function () use ($validated, $teacher, &$tuples) {
            foreach ($validated['grades'] as $row) {
                $examType = ExamType::findOrFail($row['exam_type_id']);

                $grade = StudentGrade::updateOrCreate(
                    [
                        'student_user_id' => $row['student_id'],
                        'subject_id'      => $row['subject_id'],
                        'exam_type_id'    => $row['exam_type_id'],
                    ],
                    [
                        'semester_id'      => $examType->semester_id,
                        'teacher_user_id'  => $teacher->id,
                        'score'            => $row['score'],
                        'max_score'        => $row['max_score'],
                    ]
                );

                $tuples[] = [
                    'student_user_id' => $row['student_id'],
                    'subject_id'      => $row['subject_id'],
                    'semester_id'     => $examType->semester_id,
                ];
            }

            GradeService::refreshSummaries($tuples);
        });

        return response()->json(['message' => 'Grades saved.', 'count' => count($validated['grades'])], 201);
    }

    /** GET /api/v1/grades?student_id=X&semester_id=Y */
    public function index(Request $request): JsonResponse
    {
        $actor = Auth::user();

        $studentId  = $request->integer('student_id');
        $semesterId = $request->integer('semester_id');

        if (!$studentId) {
            return response()->json(['message' => 'student_id is required.'], 422);
        }

        $student = User::findOrFail($studentId);
        $this->authorize('viewRecords', $student);

        $query = StudentGrade::where('student_user_id', $studentId)
            ->with(['subject', 'examType', 'teacher']);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        return response()->json($query->get());
    }

    /** GET /api/v1/grades/class-average?subject_id=X&exam_type_id=Y */
    public function classAverage(ClassAverageRequest $request): JsonResponse
    {
        $stats = StudentGrade::where('subject_id', $request->subject_id)
            ->where('exam_type_id', $request->exam_type_id)
            ->selectRaw('count(*) as count, avg(score/max_score*100) as average, min(score/max_score*100) as min_pct, max(score/max_score*100) as max_pct')
            ->first();

        return response()->json($stats);
    }

    /** GET /api/v1/students/{id}/report-card?semester_id=Y */
    public function reportCard(Request $request, int $id): JsonResponse
    {
        $this->authorizeReportCardAccess($id);

        $data = $this->assembler->assemble($id, $request->integer('semester_id') ?: null);

        return response()->json([
            'student'   => $data->student,
            'summaries' => $data->summaries,
            'grades'    => $data->grades->groupBy(fn($g) => "{$g->subject_id}-{$g->semester_id}"),
        ]);
    }

    /** GET /api/v1/students/{id}/report-card/pdf?semester_id=Y */
    public function reportCardPdf(Request $request, int $id): Response
    {
        $this->authorizeReportCardAccess($id);

        $semesterId = $request->integer('semester_id') ?: null;
        $data       = $this->assembler->assemble($id, $semesterId);

        $student   = $data->student;
        $semester  = $data->semester;
        $summaries = $data->summaries;
        $examTypes = $data->examTypes;
        $grades    = $data->grades->groupBy('subject_id');

        $pdf = Pdf::loadView('pdf.report_card', compact('student', 'semester', 'summaries', 'grades', 'examTypes'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("report_card_{$student->name}_{$semesterId}.pdf");
    }

    private function authorizeReportCardAccess(int $studentId): void
    {
        $student = User::findOrFail($studentId);
        $this->authorize('viewRecords', $student);
    }
}
