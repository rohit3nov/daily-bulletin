<?php

namespace app\Services\NewsApi;

use App\Contracts\NewsApiInterface;
use App\Services\NewsApi\AbstractNewsApiService;

class NYTimes extends AbstractNewsApiService implements NewsApiInterface
{
    public const API_NAME = 'NYTimes';

    protected function buildEndpoint(string $category): string
    {
        return $this->getUrl() . str_replace('{section}', strtolower($category), $this->getEndpoint());
    }
}
