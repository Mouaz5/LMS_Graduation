<?php

namespace App\Services;

use App\Models\DiagnosticAnswer;
use App\Models\DiagnosticAttempt;
use App\Models\DiagnosticQuestion;
use App\Models\KnowledgeMapResult;
use Illuminate\Support\Facades\DB;

class DiagnosticAttemptService
{
    /**
     * Record answers for a diagnostic attempt, mark it complete, and recompute
     * mastery per learning objective.
     *
     * @param  array $answers  [['question_id' => int, 'selected_option_id' => ?int], ...]
     * @return array<int, float>  mastery_percent keyed by learning_objective_id
     */
    public function submitAnswers(DiagnosticAttempt $attempt, array $answers): array
    {
        $masteryByObjective = [];

        DB::transaction(function () use ($attempt, $answers, &$masteryByObjective) {
            foreach ($answers as $ans) {
                $isCorrect = false;

                if ($ans['selected_option_id']) {
                    $question = DiagnosticQuestion::find($ans['question_id']);
                    $isCorrect = $question
                        ? $question->options()
                            ->where('id', $ans['selected_option_id'])
                            ->where('is_correct', true)
                            ->exists()
                        : false;
                }

                DiagnosticAnswer::create([
                    'attempt_id'         => $attempt->id,
                    'question_id'        => $ans['question_id'],
                    'selected_option_id' => $ans['selected_option_id'] ?? null,
                    'is_correct'         => $isCorrect,
                ]);
            }

            $attempt->update(['completed_at' => now()]);

            $savedAnswers = $attempt->answers()->with('question')->get();
            $byObjective  = $savedAnswers->groupBy('question.learning_objective_id');

            foreach ($byObjective as $objectiveId => $objectiveAnswers) {
                $total   = $objectiveAnswers->count();
                $correct = $objectiveAnswers->where('is_correct', true)->count();
                $mastery = $total > 0 ? round(($correct / $total) * 100, 2) : 0;

                KnowledgeMapResult::updateOrCreate(
                    ['student_user_id' => $attempt->student_user_id, 'learning_objective_id' => $objectiveId],
                    ['mastery_percent' => $mastery, 'last_assessed_at' => now()]
                );

                $masteryByObjective[$objectiveId] = $mastery;
            }
        });

        return $masteryByObjective;
    }
}
