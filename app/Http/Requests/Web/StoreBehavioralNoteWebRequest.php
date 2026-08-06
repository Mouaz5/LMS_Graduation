<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreBehavioralNoteWebRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'student_user_id' => 'required|exists:users,id',
            'note'            => 'required|string|max:2000',
            'severity'        => 'required|in:info,warning,critical',
            'date'            => 'required|date',
        ];
    }
}
