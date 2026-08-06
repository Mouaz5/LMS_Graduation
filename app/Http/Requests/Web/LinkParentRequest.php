<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class LinkParentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'parent_user_id' => 'required|exists:users,id',
            'relation'       => 'required|in:father,mother,guardian',
        ];
    }
}
