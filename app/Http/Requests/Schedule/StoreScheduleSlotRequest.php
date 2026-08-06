<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleSlotRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'classroom_id'    => 'required|exists:classrooms,id',
            'subject_id'      => 'required|exists:subjects,id',
            'teacher_user_id' => 'required|exists:users,id',
            'day_of_week'     => 'required|in:sunday,monday,tuesday,wednesday,thursday',
            'period_number'   => 'required|integer|min:1|max:8',
            'start_time'      => 'required|date_format:H:i',
            'end_time'        => 'required|date_format:H:i|after:start_time',
            'semester_id'     => 'required|exists:semesters,id',
        ];
    }
}
