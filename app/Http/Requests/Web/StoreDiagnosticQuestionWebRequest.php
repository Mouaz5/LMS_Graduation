<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiagnosticQuestionWebRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'subject_id'            => 'required|exists:subjects,id',
            'learning_objective_id' => 'required|exists:learning_objectives,id',
            'question_text'         => 'required|string',
            'type'                  => 'required|in:mcq,true_false',
            'options'               => 'required|array|min:2',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct'  => 'nullable',
        ];
    }
}
