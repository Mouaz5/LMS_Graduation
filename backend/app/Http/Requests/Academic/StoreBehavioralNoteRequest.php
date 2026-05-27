<?php

namespace App\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;

class StoreBehavioralNoteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'student_user_id' => 'required|exists:users,id',
            'note'            => 'required|string|max:2000',
            'severity'        => 'required|in:info,warning,critical',
            'date'            => 'required|date|date_format:Y-m-d',
        ];
    }
}
