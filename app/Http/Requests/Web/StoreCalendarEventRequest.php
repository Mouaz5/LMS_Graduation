<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalendarEventRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'date'        => 'required|date',
            'type'        => 'required|in:holiday,event,exam',
            'description' => 'required|string|max:500',
        ];
    }
}
