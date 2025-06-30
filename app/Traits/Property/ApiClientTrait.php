<?php

namespace App\Traits\Property;

use Illuminate\Support\Facades\Http;

trait ApiClientTrait
{
    protected $apiClient;

    public function getApiClient()
    {
        return $this->apiClient ?? Http::timeout(30);
    }

    public function setApiClient($apiClient): void
    {
        $this->apiClient = $apiClient;
    }
}
