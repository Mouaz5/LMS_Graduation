<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DiagnosticAnswer;
use App\Models\DiagnosticAttempt;
use App\Models\DiagnosticQuestion;
use App\Models\KnowledgeMapResult;
use App\Models\LearningObjective;
use App\Models\QuestionOption;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiagnosticWebController extends Controller
{
    // Admin: Test Builder — list subjects, objectives, questions
    public function testBuilder(Request $request)
    {
        $subjects    = Subject::orderBy('name')->get();
        $subjectId   = $request->query('subject_id');
        $subject     = $subjectId ? Subject::find($subjectId) : null;

        $objectives = $subject
            ? LearningObjective::with('children')
                ->where('subject_id', $subject->id)
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get()
            : collect();

        $questions = $subject
            ? DiagnosticQuestion::with('options', 'learningObjective')
                ->where('subject_id', $subject->id)
                ->latest()
                ->get()
            : collect();

        return view('admin.diagnostic.test-builder', compact('subjects', 'subject', 'objectives', 'questions'));
    }

    // Admin: store learning objective
    public function storeObjective(Request $request)
    {
        $data = $request->validate([
            'subject_id'  => 'required|exists:subjects,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:learning_objectives,id',
        ]);

        LearningObjective::create($data);

        return redirect()->route('admin.diagnostic.test-builder', ['subject_id' => $data['subject_id']])
            ->with('success', 'Learning objective added.');
    }

    // Admin: store question with options
    public function storeQuestion(Request $request)
    {
        $data = $request->validate([
            'subject_id'             => 'required|exists:subjects,id',
            'learning_objective_id'  => 'required|exists:learning_objectives,id',
            'question_text'          => 'required|string',
            'type'                   => 'required|in:mcq,true_false',
            'options'                => 'required|array|min:2',
            'options.*.option_text'  => 'required|string',
            'options.*.is_correct'   => 'nullable',
        ]);

        DB::transaction(function () use ($data) {
            $question = DiagnosticQuestion::create([
                'subject_id'            => $data['subject_id'],
                'learning_objective_id' => $data['learning_objective_id'],
                'question_text'         => $data['question_text'],
                'type'                  => $data['type'],
            ]);

            $correctIndex = (int) $request->input('correct_option', 0);
            foreach ($data['options'] as $i => $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['option_text'],
                    'is_correct'  => ($i === $correctIndex),
                ]);
            }
        });

        return redirect()->route('admin.diagnostic.test-builder', ['subject_id' => $data['subject_id']])
            ->with('success', 'Question added.');
    }

    // Admin: delete question
    public function destroyQuestion(DiagnosticQuestion $question)
    {
        $subjectId = $question->subject_id;
        $question->delete();

        return redirect()->route('admin.diagnostic.test-builder', ['subject_id' => $subjectId])
            ->with('success', 'Question deleted.');
    }

    // Admin/Teacher: Knowledge Map viewer (any student)
    public function knowledgeMap(Request $request)
    {
        $subjects  = Subject::orderBy('name')->get();
        $students  = \App\Models\User::where('role', 'student')->orderBy('name')->get();
        $subjectId = $request->query('subject_id');
        $studentId = $request->query('student_id');

        $subject = $subjectId ? Subject::find($subjectId) : null;
        $student = $studentId ? \App\Models\User::find($studentId) : null;

        $tree = collect();
        if ($subject && $student) {
            $masteryMap = KnowledgeMapResult::where('student_user_id', $student->id)
                ->pluck('mastery_percent', 'learning_objective_id');

            $rootObjectives = LearningObjective::with('children.children')
                ->where('subject_id', $subject->id)
                ->whereNull('parent_id')
                ->get();

            $tree = $this->buildTree($rootObjectives, $masteryMap);
        }

        return view('admin.diagnostic.knowledge-map', compact('subjects', 'students', 'subject', 'student', 'tree'));
    }

    // Student: take a diagnostic test
    public function studentTest(Request $request)
    {
        $subjects = Subject::orderBy('name')->get();
        $subjectId = $request->query('subject_id');
        $subject = $subjectId ? Subject::find($subjectId) : null;

        $attempt = null;
        $questions = collect();

        if ($subject) {
            $attempt = DiagnosticAttempt::where('student_user_id', auth()->id())
                ->where('subject_id', $subject->id)
                ->whereNull('completed_at')
                ->latest()
                ->first();

            if ($attempt) {
                $answeredIds = $attempt->answers()->pluck('question_id');
                $questions = DiagnosticQuestion::with('options')
                    ->where('subject_id', $subject->id)
                    ->whereNotIn('id', $answeredIds)
                    ->limit(20)
                    ->get();
            }
        }

        return view('student.diagnostic.test', compact('subjects', 'subject', 'attempt', 'questions'));
    }

    // Student: start a new attempt
    public function studentStartAttempt(Request $request)
    {
        $data = $request->validate(['subject_id' => 'required|exists:subjects,id']);

        // End any open attempt first
        DiagnosticAttempt::where('student_user_id', auth()->id())
            ->where('subject_id', $data['subject_id'])
            ->whereNull('completed_at')
            ->update(['completed_at' => now()]);

        DiagnosticAttempt::create([
            'student_user_id' => auth()->id(),
            'subject_id'      => $data['subject_id'],
            'started_at'      => now(),
        ]);

        return redirect()->route('student.diagnostic.test', ['subject_id' => $data['subject_id']]);
    }

    // Student: submit answers
    public function studentSubmitAttempt(Request $request, DiagnosticAttempt $attempt)
    {
        abort_if($attempt->student_user_id !== auth()->id(), 403);

        $data = $request->validate([
            'answers'   => 'required|array',
            'answers.*' => 'nullable|exists:question_options,id',
        ]);

        DB::transaction(function () use ($attempt, $data) {
            foreach ($data['answers'] as $questionId => $optionId) {
                $question = DiagnosticQuestion::find($questionId);
                if (! $question) continue;

                $isCorrect = $optionId
                    ? $question->options()->where('id', $optionId)->where('is_correct', true)->exists()
                    : false;

                DiagnosticAnswer::create([
                    'attempt_id'         => $attempt->id,
                    'question_id'        => $questionId,
                    'selected_option_id' => $optionId ?? null,
                    'is_correct'         => $isCorrect,
                ]);
            }

            $attempt->update(['completed_at' => now()]);

            $answers = $attempt->answers()->with('question')->get();
            $byObjective = $answers->groupBy('question.learning_objective_id');

            foreach ($byObjective as $objectiveId => $objectiveAnswers) {
                $total   = $objectiveAnswers->count();
                $correct = $objectiveAnswers->where('is_correct', true)->count();
                $mastery = $total > 0 ? round(($correct / $total) * 100, 2) : 0;

                KnowledgeMapResult::updateOrCreate(
                    ['student_user_id' => $attempt->student_user_id, 'learning_objective_id' => $objectiveId],
                    ['mastery_percent' => $mastery, 'last_assessed_at' => now()]
                );
            }
        });

        return redirect()->route('student.diagnostic.knowledge-map', ['subject_id' => $attempt->subject_id])
            ->with('success', 'Test submitted! Your knowledge map has been updated.');
    }

    // Student: view own knowledge map
    public function studentKnowledgeMap(Request $request)
    {
        $subjects  = Subject::orderBy('name')->get();
        $subjectId = $request->query('subject_id');
        $subject   = $subjectId ? Subject::find($subjectId) : null;

        $tree = collect();
        if ($subject) {
            $masteryMap = KnowledgeMapResult::where('student_user_id', auth()->id())
                ->pluck('mastery_percent', 'learning_objective_id');

            $rootObjectives = LearningObjective::with('children.children')
                ->where('subject_id', $subject->id)
                ->whereNull('parent_id')
                ->get();

            $tree = $this->buildTree($rootObjectives, $masteryMap);
        }

        return view('student.diagnostic.knowledge-map', compact('subjects', 'subject', 'tree'));
    }

    private function buildTree($objectives, $masteryMap): array
    {
        return $objectives->map(fn ($obj) => [
            'id'              => $obj->id,
            'name'            => $obj->name,
            'description'     => $obj->description,
            'mastery_percent' => isset($masteryMap[$obj->id]) ? (float) $masteryMap[$obj->id] : null,
            'children'        => $this->buildTree($obj->children, $masteryMap),
        ])->values()->toArray();
    }
}
