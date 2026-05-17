<?php

namespace App\Http\Requests;

use App\Support\FileUploadSecurity;
use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', ...FileUploadSecurity::mediaRules()],
            'folder_id' => 'nullable|exists:media_folders,id',
            'alt_text' => 'nullable|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimetypes' => 'File type not allowed. Allowed types: JPG, PNG, GIF, WebP, PDF',
            'file.max' => 'File size cannot exceed 5MB.',
        ];
    }
}
