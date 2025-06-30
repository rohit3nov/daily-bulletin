<?php

namespace App\Traits\Property;

trait CategoriesTrait
{
    protected array $categories = [];

    public function getCategories(): array
    {
        return $this->categories ?: $this->categories = $this->getConfig()['categories'] ?? [];
    }

    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }
}
