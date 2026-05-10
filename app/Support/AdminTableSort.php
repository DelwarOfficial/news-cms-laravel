<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AdminTableSort
{
    public static function resolve(Request $request, array $allowedSorts, string $defaultColumn = 'created_at', string $defaultDirection = 'desc'): array
    {
        $sortBy = $request->query('sort_by', $defaultColumn);
        $sortDirection = $request->query('sort_direction', $defaultDirection);

        if (! array_key_exists($sortBy, $allowedSorts) && ! in_array($sortBy, $allowedSorts, true)) {
            $sortBy = $defaultColumn;
        }

        if (! in_array($sortDirection, ['asc', 'desc'], true)) {
            $sortDirection = $defaultDirection;
        }

        return [$sortBy, $sortDirection];
    }

    public static function apply(Builder $query, array $allowedSorts, string $sortBy, string $sortDirection): Builder
    {
        $column = $allowedSorts[$sortBy] ?? $sortBy;

        return $query->orderBy($column, $sortDirection);
    }
}
