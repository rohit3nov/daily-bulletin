<?php

namespace App\Traits\Property;

trait MappingTrait
{
    protected array $mapping = [];

    public function getMapping(): array
    {
        return $this->mapping ?: $this->mapping = $this->getConfig()['mapping'] ?? [];
    }

    public function setMapping(array $mapping): void
    {
        $this->mapping = $mapping;
    }
}
