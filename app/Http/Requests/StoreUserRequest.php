<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'username' => 'required|unique:users|max:50|regex:/^[a-zA-Z0-9_-]+$/',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:10|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            'role' => 'required|exists:roles,name',
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Username can only contain letters, numbers, hyphens and underscores.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'password.min' => 'Password must be at least 10 characters long.',
        ];
    }
}
