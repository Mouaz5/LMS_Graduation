<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Grade\BulkStoreGradeRequest;
use App\Http\Requests\Grade\ClassAverageRequest;
use App\Http\Responses\ApiResponse;
use App\Models\StudentGrade;
use App\Models\User;
use App\Services\Grade\GradeEntryService;
use App\Services\Grade\ReportCardService;
use App\Services\ReportCardPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mpdf\Output\Destination;

class GradeController extends Controller
{
    public function __construct(
        private GradeEntryService $entries,
        private ReportCardService $reports,
        private ReportCardPdfService $pdf,
    ) {}

    /** POST /api/v1/grades/bulk */
    public function bulkStore(BulkStoreGradeRequest $request): JsonResponse
    {
        $count = $this->entries->storeBulk(
            $request->user(),
            $request->validated('grades'),
        );

        return ApiResponse::success(
            data: ['count' => $count],
            message: 'Grades saved.',
            status: 201,
        );
    }

    /** GET /api/v1/grades?student_id=X&semester_id=Y */
    public function index(Request $request): JsonResponse
    {
        $studentId = $request->integer('student_id');
        $semesterId = $request->integer('semester_id');

        if (! $studentId) {
            return response()->json(['message' => 'student_id is required.'], 422);
        }

        $student = User::findOrFail($studentId);
        $this->authorize('viewRecords', $student);

        $query = StudentGrade::where('student_user_id', $studentId)
            ->with(['subject', 'examType', 'teacher']);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        return ApiResponse::success(data: $query->get());
    }

    /** GET /api/v1/grades/class-average?subject_id=X&exam_type_id=Y */
    public function classAverage(ClassAverageRequest $request): JsonResponse
    {
        $stats = StudentGrade::where('subject_id', $request->subject_id)
            ->where('exam_type_id', $request->exam_type_id)
            ->selectRaw('count(*) as count, avg(score/max_score*100) as average, min(score/max_score*100) as min_pct, max(score/max_score*100) as max_pct')
            ->first();

        return ApiResponse::success(data: $stats);
    }

    /** GET /api/v1/students/{id}/report-card?semester_id=Y */
    public function reportCard(Request $request, int $id): JsonResponse
    {
        $this->authorizeReportCardAccess($id);
        $data = $this->reports->assembleById($id, $request->integer('semester_id') ?: null);

        return ApiResponse::success(data: [
            'student' => $data->student,
            'summaries' => $data->summaries,
            'grades' => $data->grades->groupBy(fn ($grade) => "{$grade->subject_id}-{$grade->semester_id}"),
        ]);
    }

    /** GET /api/v1/students/{id}/report-card/pdf?semester_id=Y */
    public function reportCardPdf(Request $request, int $id): Response
    {
        $this->authorizeReportCardAccess($id);
        $semesterId = $request->integer('semester_id') ?: null;
        $data = $this->reports->assembleById($id, $semesterId);
        $student = $data->student;
        $semester = $data->semester;
        $summaries = $data->summaries;
        $grades = $data->grades->groupBy('subject_id');
        $examTypes = $data->examTypes;

        $mpdf = $this->pdf->render(compact('student', 'semester', 'summaries', 'grades', 'examTypes'));
        $filename = "report_card_{$student->name}_{$semesterId}.pdf";

        return response($mpdf->Output($filename, Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    private function authorizeReportCardAccess(int $studentId): void
    {
        $student = User::findOrFail($studentId);
        $this->authorize('viewRecords', $student);
    }
}
