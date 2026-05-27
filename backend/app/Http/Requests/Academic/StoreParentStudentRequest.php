<?php

namespace App\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;

class StoreParentStudentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'parent_user_id'  => 'required|exists:users,id',
            'student_user_id' => 'required|exists:users,id',
            'relation'        => 'required|in:father,mother,guardian',
        ];
    }
}
