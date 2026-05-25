<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\DiagnosticAnswer;
use App\Models\DiagnosticAttempt;
use App\Models\DiagnosticQuestion;
use App\Models\KnowledgeMapResult;
use App\Models\LearningObjective;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiagnosticController extends Controller
{
    // POST /diagnostic-attempts
    public function startAttempt(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $attempt = DiagnosticAttempt::create([
            'student_user_id' => $request->user()->id,
            'subject_id'      => $data['subject_id'],
            'started_at'      => now(),
        ]);

        return response()->json(['attempt_id' => $attempt->id, 'started_at' => $attempt->started_at], 201);
    }

    // GET /diagnostic-attempts/{id}/questions
    public function getQuestions(Request $request, int $id): JsonResponse
    {
        $attempt = DiagnosticAttempt::where('id', $id)
            ->where('student_user_id', $request->user()->id)
            ->firstOrFail();

        $answeredIds = $attempt->answers()->pluck('question_id');

        $questions = DiagnosticQuestion::with('options')
            ->where('subject_id', $attempt->subject_id)
            ->whereNotIn('id', $answeredIds)
            ->limit(20)
            ->get()
            ->map(fn ($q) => [
                'id'           => $q->id,
                'question_text' => $q->question_text,
                'type'         => $q->type,
                'options'      => $q->options->map(fn ($o) => [
                    'id'          => $o->id,
                    'option_text' => $o->option_text,
                ]),
            ]);

        return response()->json(['questions' => $questions]);
    }

    // POST /diagnostic-attempts/{id}/submit
    public function submitAttempt(Request $request, int $id): JsonResponse
    {
        $attempt = DiagnosticAttempt::where('id', $id)
            ->where('student_user_id', $request->user()->id)
            ->whereNull('completed_at')
            ->firstOrFail();

        $data = $request->validate([
            'answers'                     => 'required|array',
            'answers.*.question_id'       => 'required|exists:diagnostic_questions,id',
            'answers.*.selected_option_id' => 'nullable|exists:question_options,id',
        ]);

        DB::transaction(function () use ($attempt, $data) {
            foreach ($data['answers'] as $ans) {
                $question = DiagnosticQuestion::find($ans['question_id']);
                $isCorrect = false;

                if ($ans['selected_option_id']) {
                    $isCorrect = $question->options()
                        ->where('id', $ans['selected_option_id'])
                        ->where('is_correct', true)
                        ->exists();
                }

                DiagnosticAnswer::create([
                    'attempt_id'         => $attempt->id,
                    'question_id'        => $ans['question_id'],
                    'selected_option_id' => $ans['selected_option_id'] ?? null,
                    'is_correct'         => $isCorrect,
                ]);
            }

            $attempt->update(['completed_at' => now()]);

            // Recalculate mastery per learning_objective
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

        return response()->json(['message' => 'Attempt submitted successfully.']);
    }

    // GET /knowledge-map?student_id=X&subject_id=Y
    public function knowledgeMap(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $studentId = (int) $request->student_id;
        $subjectId = (int) $request->subject_id;

        // Authorization: student sees own, admin/teacher sees all
        $user = $request->user();
        if ($user->role === 'student' && $user->id !== $studentId) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        if ($user->role === 'parent') {
            $childIds = $user->children()->pluck('users.id')->toArray();
            if (! in_array($studentId, $childIds)) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }
        }

        $masteryMap = KnowledgeMapResult::where('student_user_id', $studentId)
            ->pluck('mastery_percent', 'learning_objective_id');

        $rootObjectives = LearningObjective::with('children.children')
            ->where('subject_id', $subjectId)
            ->whereNull('parent_id')
            ->get();

        $tree = $this->buildTree($rootObjectives, $masteryMap);

        return response()->json(['tree' => $tree]);
    }

    private function buildTree($objectives, $masteryMap): array
    {
        return $objectives->map(fn ($obj) => [
            'id'              => $obj->id,
            'name'            => $obj->name,
            'description'     => $obj->description,
            'mastery_percent' => $masteryMap[$obj->id] ?? null,
            'children'        => $this->buildTree($obj->children, $masteryMap),
        ])->values()->toArray();
    }
}
