<?php

namespace App\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolCalendarRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'school_id'   => 'required|exists:schools,id',
            'date'        => 'required|date',
            'type'        => 'required|in:holiday,event,exam',
            'description' => 'required|string|max:500',
        ];
    }
}
