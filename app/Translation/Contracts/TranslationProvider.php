<?php

namespace App\Translation\Contracts;

use App\Translation\TranslationResult;

interface TranslationProvider
{
    public function translate(string $text, string $from, string $to, array $opts = []): TranslationResult;
    public function getProviderName(): string;
    public function isAvailable(): bool;
}
