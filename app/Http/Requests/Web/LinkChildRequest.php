<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class LinkChildRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'student_user_id' => 'required|exists:users,id',
            'relation'        => 'required|in:father,mother,guardian',
        ];
    }
}
