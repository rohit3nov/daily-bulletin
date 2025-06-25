<?php

namespace App\Traits\Property;
trait EndpointTrait
{
    protected ?string $endpoint = null;

    public function getEndpoint(): ?string
    {
        return $this->endpoint ??= $this->getConfig()['endpoint'] ?? null;
    }

    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }
}
