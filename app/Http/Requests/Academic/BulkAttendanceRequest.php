<?php

namespace App\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;

class BulkAttendanceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'classroom_id'         => 'required|exists:classrooms,id',
            'date'                 => 'required|date|date_format:Y-m-d',
            'schedule_slot_id'     => 'nullable|exists:schedule_slots,id',
            'entries'              => 'required|array|min:1',
            'entries.*.student_id' => 'required|exists:users,id',
            'entries.*.status'     => 'required|in:present,absent,late,excused',
        ];
    }
}
