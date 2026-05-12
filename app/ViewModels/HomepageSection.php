<?php

namespace App\ViewModels;

class HomepageSection
{
    public function __construct(
        public readonly string $key,
        public readonly string $source,
        public readonly array $articles = [],
        public readonly array $meta = [],
    ) {
    }

    public function first(): ?array
    {
        return $this->articles[0] ?? null;
    }

    public function slice(int $offset, ?int $length = null): array
    {
        return array_slice($this->articles, $offset, $length);
    }

    public function isEmpty(): bool
    {
        return $this->articles === [];
    }
}
