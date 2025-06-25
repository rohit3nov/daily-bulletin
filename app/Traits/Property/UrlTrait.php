<?php

namespace App\Traits\Property;

trait UrlTrait
{
    protected ?string $url = null;

    public function getUrl(): ?string
    {
        return $this->url ??= $this->getConfig()['url'] ?? null;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
