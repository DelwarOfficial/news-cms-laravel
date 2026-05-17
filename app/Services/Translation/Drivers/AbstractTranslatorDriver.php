<?php

namespace App\Services\Translation\Drivers;

use App\Services\Translation\Contracts\TranslatorDriver;

abstract class AbstractTranslatorDriver implements TranslatorDriver
{
    public function __construct(
        protected readonly array $config,
    ) {
    }
}
