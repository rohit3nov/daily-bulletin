<?php

namespace App\Traits\Property;

trait SearchKeyTrait
{
    protected ?string $searchKey = null;

    public function getSearchKey(): ?string
    {
        return $this->searchKey ??= $this->getConfig()['search_key'] ?? null;
    }

    public function setSearchKey(string $searchKey): void
    {
        $this->searchKey = $searchKey;
    }
}
