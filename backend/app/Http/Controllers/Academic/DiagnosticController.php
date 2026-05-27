<?php

namespace App\Http\Controllers\Academic;

use App\Domain\MasteryLevel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Diagnostic\KnowledgeMapRequest;
use App\Http\Requests\Diagnostic\StartAttemptRequest;
use App\Http\Requests\Diagnostic\SubmitAttemptRequest;
use App\Models\DiagnosticAttempt;
use App\Models\KnowledgeMapResult;
use App\Models\LearningObjective;
use App\Models\User;
use App\Services\DiagnosticAttemptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiagnosticController extends Controller
{
    public function __construct(private DiagnosticAttemptService $service) {}

    // POST /diagnostic-attempts
    public function startAttempt(StartAttemptRequest $request): JsonResponse
    {
        $data = $request->validated();

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
    public function submitAttempt(SubmitAttemptRequest $request, int $id): JsonResponse
    {
        $attempt = DiagnosticAttempt::where('id', $id)
            ->where('student_user_id', $request->user()->id)
            ->whereNull('completed_at')
            ->firstOrFail();

        $this->service->submitAnswers($attempt, $request->validated()['answers']);

        return response()->json(['message' => 'Attempt submitted successfully.']);
    }

    // GET /knowledge-map?student_id=X&subject_id=Y
    public function knowledgeMap(KnowledgeMapRequest $request): JsonResponse
    {
        $studentId = $request->integer('student_id');
        $subjectId = $request->integer('subject_id');

        $student = User::findOrFail($studentId);
        $this->authorize('viewRecords', $student);

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
        return $objectives->map(function ($obj) use ($masteryMap) {
            $pct = $masteryMap[$obj->id] ?? null;
            return [
                'id'              => $obj->id,
                'name'            => $obj->name,
                'description'     => $obj->description,
                'mastery_percent' => $pct,
                'level'           => MasteryLevel::fromPercent($pct),
                'children'        => $this->buildTree($obj->children, $masteryMap),
            ];
        })->values()->toArray();
    }
}
