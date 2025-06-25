<?php

namespace App\Traits\Property;

use GuzzleHttp\Client;

trait ClientTrait
{
    protected Client $client;

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(string $baseUrl): void
    {
        $this->client = new Client(['base_uri' => $baseUrl, 'timeout' => 30]);
    }
}
