<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationPrompt extends Model
{
    protected $fillable = [
        'name',
        'prompt_template',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function getTemplate(string $name): ?string
    {
        return static::where('name', $name)
            ->where('is_active', true)
            ->value('prompt_template');
    }

    public function fillTemplate(array $vars): string
    {
        return strtr($this->prompt_template, $vars);
    }
}
