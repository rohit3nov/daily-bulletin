<?php

namespace App\Traits\Property;

trait RateLimitTrait
{
    protected int $rateLimit;

    public function getRateLimit(): int
    {
        return $this->rateLimit ?: $this->rateLimit = $this->getConfig()['rate_limit'] ?? [];
    }

    public function setRateLimit(int $rateLimit): void
    {
        $this->rateLimit = $rateLimit;
    }
}
