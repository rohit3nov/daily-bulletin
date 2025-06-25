<?php

namespace App\Contracts;

interface NewsApiInterface
{
    public function getName(): string;
    public function getRateLimit(): int;

    public function fetch(string $category): array;
}
