<?php

namespace Tests\Feature\Commands;

use App\Jobs\FetchNews;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class FetchNewsCommandTest extends TestCase
{
    /** @test */
    public function it_dispatches_fetch_jobs_for_each_configured_source_and_category()
    {
        // Prevent actual job handling
        Bus::fake();

        // Simulated config structure with actual expected keys
        Config::set('services.newsapi.sources', [
            'NewsOrg'  => [
                'url'          => 'https://newsapi.org',
                'endpoint'     => '/v2/top-headlines',
                'search_key'   => 'q',
                'response_key' => 'articles',
                'rate_limit'   => 10,
                'query_params' => [
                    'country'  => 'us',
                    'pageSize' => 20,
                    'apiKey'   => 'dummy-api-key'
                ],
                'mapping'      => [
                    'title'        => 'title',
                    'description'  => 'description',
                    'url'          => 'url',
                    'url_to_image' => 'urlToImage',
                    'published_at' => 'publishedAt',
                    'source'       => 'source.name',
                    'source_id'    => 'source.id',
                    'author'       => 'author',
                    'content'      => 'content'
                ]
            ],
            'Guardian' => [
                'url'          => 'https://content.guardianapis.com',
                'endpoint'     => 'search',
                'search_key'   => 'q',
                'response_key' => 'response.results',
                'rate_limit'   => 10,
                'query_params' => [
                    'show-fields' => 'thumbnail,bodyText,byline',
                    'api-key'     => 'dummy-guardian-key'
                ],
                'mapping'      => [
                    'title'        => 'webTitle',
                    'description'  => 'description',
                    'url'          => 'webUrl',
                    'url_to_image' => 'fields.thumbnail',
                    'published_at' => 'webPublicationDate',
                    'source'       => 'The Guardian',
                    'source_id'    => 'guardian',
                    'author'       => 'fields.byline',
                    'content'      => 'fields.bodyText'
                ]
            ],
        ]);

        Config::set('services.newsapi.categories', ['Business', 'Technology']);

        // Act
        Artisan::call('fetch:news');

        // Assert that 2 sources × 2 categories = 4 jobs were dispatched
        Bus::assertDispatchedTimes(FetchNews::class, 4);

        // Optional: make sure a specific category/source job is dispatched
        Bus::assertDispatched(FetchNews::class, function ($job) {
            return $job->category === 'Business';
        });
    }

    /** @test */
    public function it_does_not_dispatch_jobs_if_no_sources_are_configured()
    {
        Bus::fake();

        Config::set('services.newsapi.sources', []);
        Config::set('services.newsapi.categories', ['General']);

        Artisan::call('fetch:news');

        Bus::assertNothingDispatched();
    }
}
