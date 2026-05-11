<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name',
        'code',
        'locale',
        'flag',
        'is_rtl',
        'is_active',
        'is_default',
        'order',
    ];

    public static function idForLocale(?string $locale = null): int
    {
        $locale = in_array($locale, ['en', 'bn'], true)
            ? $locale
            : (in_array(config('app.locale'), ['en', 'bn'], true) ? config('app.locale') : 'bn');

        $language = self::query()
            ->where('code', $locale)
            ->orWhere('locale', 'like', $locale.'%')
            ->first();

        if ($language) {
            return $language->id;
        }

        $defaultLanguage = self::query()->where('is_default', true)->first()
            ?? self::query()->first();

        if ($defaultLanguage) {
            return $defaultLanguage->id;
        }

        return self::create([
            'name' => $locale === 'en' ? 'English' : 'Bengali',
            'code' => $locale,
            'locale' => $locale === 'en' ? 'en_US' : 'bn_BD',
            'is_default' => true,
            'is_active' => true,
            'order' => 1,
        ])->id;
    }
}
