<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:App\Models\System\User'],
            'password' => ['required', 'string', 'min:8'],
            'token_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
