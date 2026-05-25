<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use App\Models\Semester;
use App\Models\StudentGrade;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherSubjectClassroom;
use App\Models\User;
use App\Services\GradeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherGradeController extends Controller
{
    public function entry(Request $request): View
    {
        $teacher = Auth::user();

        $assignments = TeacherSubjectClassroom::where('teacher_user_id', $teacher->id)
            ->with(['subject', 'classroom'])
            ->get();

        $semesters  = Semester::with('academicYear')->orderByDesc('id')->get();
        $selectedSemesterId  = $request->integer('semester_id') ?: $semesters->first()?->id;
        $selectedSubjectId   = $request->integer('subject_id');
        $selectedClassroomId = $request->integer('classroom_id');
        $selectedExamTypeId  = $request->integer('exam_type_id');

        $examTypes = $selectedSemesterId
            ? ExamType::where('semester_id', $selectedSemesterId)->get()
            : collect();

        $students = collect();
        $existingGrades = collect();

        if ($selectedSubjectId && $selectedClassroomId) {
            $students = User::whereHas('studentProfile', fn($q) => $q->where('classroom_id', $selectedClassroomId))
                ->with('studentProfile')
                ->orderBy('name')
                ->get();

            if ($selectedExamTypeId) {
                $existingGrades = StudentGrade::where('subject_id', $selectedSubjectId)
                    ->where('exam_type_id', $selectedExamTypeId)
                    ->whereIn('student_user_id', $students->pluck('id'))
                    ->get()
                    ->keyBy('student_user_id');
            }
        }

        return view('teacher.grades.entry', compact(
            'assignments', 'semesters', 'examTypes', 'students', 'existingGrades',
            'selectedSemesterId', 'selectedSubjectId', 'selectedClassroomId', 'selectedExamTypeId'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user();

        $request->validate([
            'subject_id'    => 'required|integer|exists:subjects,id',
            'exam_type_id'  => 'required|integer|exists:exam_types,id',
            'scores'        => 'required|array',
            'scores.*'      => 'nullable|numeric|min:0',
            'max_score'     => 'required|numeric|min:0.01',
        ]);

        $subjectId   = $request->integer('subject_id');
        $examTypeId  = $request->integer('exam_type_id');
        $maxScore    = $request->float('max_score');
        $examType    = ExamType::findOrFail($examTypeId);

        // Verify teacher is assigned to this subject
        $isAssigned = TeacherSubjectClassroom::where('teacher_user_id', $teacher->id)
            ->where('subject_id', $subjectId)
            ->exists();

        if (!$isAssigned) {
            return back()->withErrors(['subject_id' => 'You are not assigned to this subject.']);
        }

        $tuples = [];

        DB::transaction(function () use ($request, $teacher, $subjectId, $examTypeId, $examType, $maxScore, &$tuples) {
            foreach ($request->scores as $studentId => $score) {
                if ($score === null || $score === '') {
                    continue;
                }
                if ((float)$score > $maxScore) {
                    continue; // skip invalid rows silently (UI validates beforehand)
                }

                StudentGrade::updateOrCreate(
                    [
                        'student_user_id' => $studentId,
                        'subject_id'      => $subjectId,
                        'exam_type_id'    => $examTypeId,
                    ],
                    [
                        'semester_id'     => $examType->semester_id,
                        'teacher_user_id' => $teacher->id,
                        'score'           => $score,
                        'max_score'       => $maxScore,
                    ]
                );

                $tuples[] = [
                    'student_user_id' => $studentId,
                    'subject_id'      => $subjectId,
                    'semester_id'     => $examType->semester_id,
                ];
            }

            GradeService::refreshSummaries($tuples);
        });

        return back()->with('success', 'Grades saved successfully.');
    }
}
