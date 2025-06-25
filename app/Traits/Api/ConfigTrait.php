<?php


namespace App\Traits\Api;

trait ConfigTrait
{
    protected array $config = [];

    public function getConfig(): array
    {
        return $this->config;
    }
    public function setConfig(): void
    {
        $this->config = config('services.newsapi.sources.' . $this->getName(), []);
    }
}
