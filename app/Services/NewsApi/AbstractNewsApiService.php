<?php

namespace App\Services\NewsApi;

use App\Traits\Api\ConfigTrait;
use App\Traits\Property;
use Exception;
use Illuminate\Support\Facades\Log;


abstract class AbstractNewsApiService
{
    use ConfigTrait;
    use Property\UrlTrait;
    use Property\EndpointTrait;
    use Property\QueryParamsTrait;
    use Property\SearchKeyTrait;
    use Property\ResponseKeyTrait;
    use Property\MappingTrait;
    use Property\ClientTrait;
    use Property\RateLimitTrait;

    public function __construct()
    {
        $this->setConfig();
        $this->setClient($this->getConfig()["url"]);
    }

    public function getName(): string
    {
        return static::API_NAME;
    }

    public function fetch(string $category): array
    {
        try {
            $response = $this->client->get($this->getEndpoint(), [
                'query' => [...$this->getQueryParams(), $this->getSearchKey() => $category]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            $articles = data_get($data ?? [], $this->getResponseKey(), []);

            return array_map(function ($article) {
                $mapped = [];
                foreach ($this->getMapping() as $key => $path) {
                    $mapped[$key] = data_get($article, $path);
                }
                return $mapped;
            }, $articles);

        } catch (Exception $e) {

            Log::error("Failed to fetch from {$this->getName()}: " . $e->getMessage());

            return [];
        }

    }
}
