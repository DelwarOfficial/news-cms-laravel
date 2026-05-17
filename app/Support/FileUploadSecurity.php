<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileUploadSecurity
{
    public const MAX_UPLOAD_KB = 5120;

    public const IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    public const DOCUMENT_MIMES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    public static function mediaMimes(bool $includeDocuments = true): array
    {
        return $includeDocuments
            ? [...self::IMAGE_MIMES, ...self::DOCUMENT_MIMES]
            : self::IMAGE_MIMES;
    }

    public static function imageRules(): array
    {
        return [
            'image',
            'mimetypes:' . implode(',', self::IMAGE_MIMES),
            'max:' . self::MAX_UPLOAD_KB,
        ];
    }

    public static function mediaRules(bool $includeDocuments = true): array
    {
        return [
            'file',
            'mimetypes:' . implode(',', self::mediaMimes($includeDocuments)),
            'max:' . self::MAX_UPLOAD_KB,
        ];
    }

    public static function storageName(UploadedFile $file): string
    {
        return Str::uuid() . '.' . self::extensionForMime((string) $file->getMimeType(), $file);
    }

    private static function extensionForMime(string $mime, UploadedFile $file): string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            default => strtolower($file->guessExtension() ?: 'bin'),
        };
    }
}
