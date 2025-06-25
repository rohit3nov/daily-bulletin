<?php

namespace App\Traits\Property;

trait QueryParamsTrait
{
    protected array $queryParams = [];

    public function getQueryParams(): array
    {
        return $this->queryParams ?: $this->queryParams = $this->getConfig()['query_params'] ?? [];
    }

    public function setQueryParams(array $params): void
    {
        $this->queryParams = $params;
    }
}
