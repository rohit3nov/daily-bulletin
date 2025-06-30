<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\Http;

trait NewsApiMockTrait
{
    protected function fakeAllNewsApis(): void
    {
        Http::fake(
            [
                'https://newsapi.org/*'              => $this->newsApiSequence(),
                'https://content.guardianapis.com/*' => $this->guardianApiSequence(),
                'https://api.nytimes.com/*'          => $this->nyTimesApiSequence(),
            ]
        );
    }

    protected function newsApiSequence()
    {
        $sequence = Http::sequence();
        foreach (range(1, 5) as $i) {
            $sequence->push(
                [
                    'status'       => 'ok',
                    'totalResults' => 5,
                    'articles'     => [
                        [
                            'source'      => ['id' => null, 'name' => 'BBC News'],
                            'author'      => null,
                            'title'       => "NewsAPI Article $i",
                            'description' => "Description $i",
                            'url'         => "https://newsapi.org/article$i",
                            'urlToImage'  => "https://newsapi.org/image$i.jpg",
                            'publishedAt' => now()->subHours($i)->toIso8601String(),
                            'content'     => "Content $i"
                        ]
                    ]
                ]
            );
        }

        return $sequence;
    }

    protected function guardianApiSequence()
    {
        $sequence = Http::sequence();
        foreach (range(1, 5) as $i) {
            $sequence->push(
                [
                    'response' => [
                        'status'  => 'ok',
                        'results' => [
                            [
                                'id'                 => "world/guardian-article-$i",
                                'type'               => 'article',
                                'sectionName'        => 'World',
                                'webTitle'           => "Guardian Title $i",
                                'webUrl'             => "https://guardian.com/world/article$i",
                                'webPublicationDate' => now()->subMinutes($i * 10)->toIso8601String(),
                                'fields'             => [
                                    'bodyText'  => "Guardian article $i full body text",
                                    'thumbnail' => "https://guardian.com/images/image$i.jpg",
                                    'byline'    => "Guardian Author $i"
                                ]
                            ]
                        ]
                    ]
                ]
            );
        }

        return $sequence;
    }

    protected function nyTimesApiSequence()
    {
        $sequence = Http::sequence();
        foreach (range(1, 5) as $i) {
            $sequence->push(
                [
                    'status'  => 'OK',
                    'results' => [
                        [
                            'title'          => "NYTimes Title $i",
                            'abstract'       => "NYTimes abstract $i",
                            'url'            => "https://nytimes.com/article$i",
                            'byline'         => "By NYTimes Author $i",
                            'published_date' => now()->subDays($i)->toDateTimeString(),
                            'section'        => 'World',
                            'multimedia'     => [
                                ['url' => "https://nytimes.com/images/image$i.jpg"]
                            ]
                        ]
                    ]
                ]
            );
        }

        return $sequence;
    }
}
