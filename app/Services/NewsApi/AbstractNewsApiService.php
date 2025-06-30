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
    use Property\CategoriesTrait;
    use Property\RateLimitTrait;
    use Property\ApiClientTrait;

    public function __construct()
    {
        $this->setConfig();
    }

    public function getName(): string
    {
        return static::API_NAME;
    }

    protected function buildEndpoint(string $category): string
    {
        return $this->getUrl() . $this->getEndpoint();
    }

    public function fetch(string $category): array
    {
        try {

            $query = $this->buildQuery($category);

            $endpoint = $this->buildEndpoint($category);

            $this->logFetchAttempt($category, $endpoint, $query);

            $response = $this->getApiClient()->get($endpoint, $query);

            return $this->transformResponse($response, $category);

        } catch (Exception $e) {

            $this->logFetchError($e);

            return [];
        }
    }

    protected function buildQuery(string $category): array
    {
        $query = $this->getQueryParams();
        $searchKey = $this->getSearchKey();

        if ($searchKey) {
            $query[$searchKey] = $category;
        }

        return $query;
    }

    protected function transformResponse($response, string $category): array
    {
        $data = json_decode($response->getBody()->getContents(), true);

        $articles = data_get($data, $this->getResponseKey(), []);

        logger()->info("[{$this->getName()}][$category] Articles", [
            'articles' => $articles,
        ]);

        return array_map(fn($article) => $this->mapArticle($article), $articles);
    }

    protected function mapArticle(array $article): array
    {
        $mapped = [];

        foreach ($this->getMapping() as $key => $path) {
            if (str_starts_with($path, 'Text:')) {
                $mapped[$key] = substr($path, strlen('Text:'));
                continue;
            }

            $mapped[$key] = data_get($article, $path);
        }

        return $mapped;
    }

    protected function logFetchAttempt(string $category, string $endpoint, array $query): void
    {
        logger()->info("Fetching [{$this->getName()}][$category]", [
            'endpoint' => $endpoint,
            'query' => $query,
        ]);
    }

    protected function logFetchError(Exception $e): void
    {
        Log::error("Failed to fetch from {$this->getName()}: " . $e->getMessage());
    }
}
