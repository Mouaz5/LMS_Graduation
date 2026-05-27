<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreDiagnosticQuestionRequest;
use App\Http\Requests\Academic\StoreLearningObjectiveRequest;
use App\Models\DiagnosticQuestion;
use App\Models\LearningObjective;
use App\Models\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LearningObjectiveController extends Controller
{
    // POST /learning-objectives  (admin only)
    public function store(StoreLearningObjectiveRequest $request): JsonResponse
    {
        $objective = LearningObjective::create($request->validated());

        return response()->json($objective->load('subject'), 201);
    }

    // POST /diagnostic-questions  (admin only)
    public function storeQuestion(StoreDiagnosticQuestionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $question = DB::transaction(function () use ($data) {
            $question = DiagnosticQuestion::create([
                'subject_id'            => $data['subject_id'],
                'learning_objective_id' => $data['learning_objective_id'],
                'question_text'         => $data['question_text'],
                'type'                  => $data['type'],
            ]);

            foreach ($data['options'] as $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['option_text'],
                    'is_correct'  => $opt['is_correct'],
                ]);
            }

            return $question->load('options', 'learningObjective');
        });

        return response()->json($question, 201);
    }
}
