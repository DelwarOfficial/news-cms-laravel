<?php

namespace App\Services\Translation\Contracts;

interface TranslatorDriver
{
    public function translate(string $prompt): ?string;
}
