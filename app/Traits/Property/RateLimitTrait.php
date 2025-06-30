<?php

namespace App\Traits\Property;

trait RateLimitTrait
{
    protected int $rateLimit = 0;

    public function getRateLimit(): int
    {
        return $this->rateLimit ?: $this->rateLimit = $this->getConfig()['rate_limit'] ?? 10;
    }

    public function setRateLimit(int $rateLimit): void
    {
        $this->rateLimit = $rateLimit;
    }
}
