<?php

namespace app\Services\NewsApi;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class Guardian
{
    public function fetch(): array
    {
        $apiKey = config('services.guardian.key');
        $url    = "https://content.guardianapis.com/search?api-key={$apiKey}&show-fields=thumbnail,bodyText,byline";

        $response = Http::get($url);

        if ($response->failed()) {
            return [];
        }

        return collect($response->json('response.results', []))->map(function ($item) {
            return [
                'title'        => $item['webTitle'] ?? null,
                'description'  => null,
                'url'          => $item['webUrl'] ?? null,
                'url_to_image' => $item['fields']['thumbnail'] ?? null,
                'published_at' => isset($item['webPublicationDate']) ? Carbon::parse($item['webPublicationDate']) : null,
                'source_id'    => 'guardian',
                'source'       => 'The Guardian',
                'author'       => $item['fields']['byline'] ?? null,
                'content'      => $item['fields']['bodyText'] ?? null,
            ];
        })->all();
    }
}
