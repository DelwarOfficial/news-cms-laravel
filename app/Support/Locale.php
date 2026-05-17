<?php

namespace App\Support;

class Locale
{
    public static function default(): string
    {
        return self::normalize(config('locales.default', config('app.locale', 'bn')));
    }

    public static function fallback(): string
    {
        return self::normalize(config('locales.fallback', config('app.fallback_locale', 'bn')));
    }

    public static function supported(): array
    {
        return array_values(array_unique(config('locales.supported', ['bn', 'en'])));
    }

    public static function normalize(?string $locale): string
    {
        $locale = strtolower((string) $locale);
        $locale = str_replace('_', '-', $locale);
        $locale = explode('-', $locale)[0] ?: self::fallbackRaw();

        return in_array($locale, self::supported(), true) ? $locale : self::fallbackRaw();
    }

    public static function isSupported(?string $locale): bool
    {
        $locale = strtolower((string) $locale);
        $locale = str_replace('_', '-', $locale);
        $locale = explode('-', $locale)[0] ?: '';

        return in_array($locale, self::supported(), true);
    }

    private static function fallbackRaw(): string
    {
        $fallback = strtolower((string) config('locales.fallback', config('app.fallback_locale', 'bn')));
        $fallback = explode('-', str_replace('_', '-', $fallback))[0] ?: 'bn';

        return in_array($fallback, config('locales.supported', ['bn', 'en']), true) ? $fallback : 'bn';
    }
}
