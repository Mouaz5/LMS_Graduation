<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\DiagnosticQuestion;
use App\Models\LearningObjective;
use App\Models\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningObjectiveController extends Controller
{
    // POST /learning-objectives  (admin only)
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject_id'  => 'required|exists:subjects,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:learning_objectives,id',
        ]);

        $objective = LearningObjective::create($data);

        return response()->json($objective->load('subject'), 201);
    }

    // POST /diagnostic-questions  (admin only)
    public function storeQuestion(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject_id'             => 'required|exists:subjects,id',
            'learning_objective_id'  => 'required|exists:learning_objectives,id',
            'question_text'          => 'required|string',
            'type'                   => 'required|in:mcq,true_false',
            'options'                => 'required|array|min:2',
            'options.*.option_text'  => 'required|string',
            'options.*.is_correct'   => 'required|boolean',
        ]);

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
