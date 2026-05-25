<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use App\Models\GradeSummary;
use App\Models\StudentGrade;
use App\Models\TeacherSubjectClassroom;
use App\Models\User;
use App\Services\GradeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GradeController extends Controller
{
    /** POST /api/v1/grades/bulk */
    public function bulkStore(Request $request): JsonResponse
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'grades'                 => 'required|array|min:1',
            'grades.*.student_id'    => 'required|integer|exists:users,id',
            'grades.*.subject_id'    => 'required|integer|exists:subjects,id',
            'grades.*.exam_type_id'  => 'required|integer|exists:exam_types,id',
            'grades.*.score'         => 'required|numeric|min:0',
            'grades.*.max_score'     => 'required|numeric|min:0.01',
        ]);

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

        // Authorization
        if ($actor->role === 'student' && $actor->id !== $studentId) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($actor->role === 'parent') {
            $children = $actor->children()->pluck('users.id');
            if (!$children->contains($studentId)) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }
        }

        $query = StudentGrade::where('student_user_id', $studentId)
            ->with(['subject', 'examType', 'teacher']);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        return response()->json($query->get());
    }

    /** GET /api/v1/grades/class-average?subject_id=X&exam_type_id=Y */
    public function classAverage(Request $request): JsonResponse
    {
        $request->validate([
            'subject_id'   => 'required|integer|exists:subjects,id',
            'exam_type_id' => 'required|integer|exists:exam_types,id',
        ]);

        $stats = StudentGrade::where('subject_id', $request->subject_id)
            ->where('exam_type_id', $request->exam_type_id)
            ->selectRaw('count(*) as count, avg(score/max_score*100) as average, min(score/max_score*100) as min_pct, max(score/max_score*100) as max_pct')
            ->first();

        return response()->json($stats);
    }

    /** GET /api/v1/students/{id}/report-card?semester_id=Y */
    public function reportCard(Request $request, int $id): JsonResponse
    {
        $actor = Auth::user();
        $this->authorizeReportCardAccess($actor, $id);

        $semesterId = $request->integer('semester_id');

        $summaries = GradeSummary::where('student_user_id', $id)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->with(['subject', 'semester.academicYear'])
            ->get();

        $grades = StudentGrade::where('student_user_id', $id)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->with(['subject', 'examType'])
            ->get()
            ->groupBy(fn($g) => "{$g->subject_id}-{$g->semester_id}");

        $student = User::with('studentProfile.classroom.grade')->find($id);

        return response()->json([
            'student'   => $student,
            'summaries' => $summaries,
            'grades'    => $grades,
        ]);
    }

    /** GET /api/v1/students/{id}/report-card/pdf?semester_id=Y */
    public function reportCardPdf(Request $request, int $id): Response
    {
        $actor = Auth::user();
        $this->authorizeReportCardAccess($actor, $id);

        $semesterId = $request->integer('semester_id');

        $student  = User::with('studentProfile.classroom.grade')->findOrFail($id);
        $semester = $semesterId ? \App\Models\Semester::with('academicYear')->find($semesterId) : null;

        $summaries = GradeSummary::where('student_user_id', $id)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->with('subject')
            ->get();

        $grades = StudentGrade::where('student_user_id', $id)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->with(['subject', 'examType'])
            ->get()
            ->groupBy('subject_id');

        $examTypes = $semesterId
            ? ExamType::where('semester_id', $semesterId)->orderBy('id')->get()
            : collect();

        $pdf = Pdf::loadView('pdf.report_card', compact('student', 'semester', 'summaries', 'grades', 'examTypes'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("report_card_{$student->name}_{$semesterId}.pdf");
    }

    private function authorizeReportCardAccess($actor, int $studentId): void
    {
        if (in_array($actor->role, ['admin', 'teacher'])) {
            return;
        }
        if ($actor->role === 'student' && $actor->id === $studentId) {
            return;
        }
        if ($actor->role === 'parent') {
            $children = $actor->children()->pluck('users.id');
            if ($children->contains($studentId)) {
                return;
            }
        }
        abort(403);
    }
}
