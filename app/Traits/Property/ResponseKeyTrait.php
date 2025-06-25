<?php

namespace App\Traits\Property;

trait ResponseKeyTrait
{
    protected ?string $responseKey = null;

    public function getResponseKey(): ?string
    {
        return $this->responseKey ??= $this->getConfig()['response_key'] ?? null;
    }

    public function setResponseKey(string $responseKey): void
    {
        $this->responseKey = $responseKey;
    }
}
