<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    protected $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|max:10240|mimetypes:' . implode(',', $this->allowedMimes),
            'folder_id' => 'nullable|exists:media_folders,id',
            'alt_text' => 'nullable|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimetypes' => 'File type not allowed. Allowed types: JPG, PNG, GIF, WebP, PDF',
            'file.max' => 'File size cannot exceed 10MB.',
        ];
    }
}
