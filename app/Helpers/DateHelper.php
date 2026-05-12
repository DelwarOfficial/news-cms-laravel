<?php

namespace App\Helpers;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class DateHelper
{
    public static function getBengaliDate(?CarbonInterface $date = null): string
    {
        $date = $date ?? Carbon::now();
        
        $dayName = $date->format('l');
        $dayNum = $date->format('d');
        $month = $date->format('F');
        $year = $date->format('Y');

        $bengaliDay = match($dayName) {
            'Sunday' => 'রবিবার',
            'Monday' => 'সোমবার',
            'Tuesday' => 'মঙ্গলবার',
            'Wednesday' => 'বুধবার',
            'Thursday' => 'বৃহস্পতিবার',
            'Friday' => 'শুক্রবার',
            'Saturday' => 'শনিবার',
            default => $dayName,
        };

        $bengaliMonth = match($month) {
            'January' => 'জানুয়ারি',
            'February' => 'ফেব্রুয়ারি',
            'March' => 'মার্চ',
            'April' => 'এপ্রিল',
            'May' => 'মে',
            'June' => 'জুন',
            'July' => 'জুলাই',
            'August' => 'আগস্ট',
            'September' => 'সেপ্টেম্বর',
            'October' => 'অক্টোবর',
            'November' => 'নভেম্বর',
            'December' => 'ডিসেম্বর',
            default => $month,
        };

        $day = self::convertToBengali($dayNum);
        $year = self::convertToBengali($year);

        return $bengaliDay . ', ' . $day . ' ' . $bengaliMonth . ' ' . $year;
    }

    public static function timeAgo(CarbonInterface|string|null $date): string
    {
        if (!$date) {
            return '';
        }

        $date = $date instanceof CarbonInterface ? $date : Carbon::parse($date);
        $seconds = abs($date->diffInSeconds(now()));

        if ($seconds < 10) {
            return 'এইমাত্র';
        }

        [$value, $unit] = match (true) {
            $seconds >= 31536000 => [floor($seconds / 31536000), 'বছর'],
            $seconds >= 2592000 => [floor($seconds / 2592000), 'মাস'],
            $seconds >= 604800 => [floor($seconds / 604800), 'সপ্তাহ'],
            $seconds >= 86400 => [floor($seconds / 86400), 'দিন'],
            $seconds >= 3600 => [floor($seconds / 3600), 'ঘণ্টা'],
            $seconds >= 60 => [floor($seconds / 60), 'মিনিট'],
            default => [$seconds, 'সেকেন্ড'],
        };

        $suffix = $date->isFuture() ? 'পর' : 'আগে';

        return self::convertToBengali((string) $value) . " {$unit} {$suffix}";
    }

    public static function convertToBengali(string $number): string
    {
        $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $ben = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($eng, $ben, $number);
    }
}
