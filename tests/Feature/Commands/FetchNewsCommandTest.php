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

        $sources = config('services.newsapi.sources');
        foreach ($sources as $key => &$source) {
            $source['categories'] = array_slice($source['categories'], 0, 2); // Keep only 2 categories
        }

        // Set the modified config for the test
        Config::set('services.newsapi.sources', $sources);

        Artisan::call('fetch:news');

        Bus::assertDispatchedTimes(FetchNews::class, 6);

        // making sure a specific category/source job is dispatched
        Bus::assertDispatched(FetchNews::class, function ($job) {
            return strtolower($job->getCategory()) === 'general';
        });
    }

    /** @test */
    public function it_does_not_dispatch_jobs_if_no_sources_are_configured()
    {
        Bus::fake();

        Config::set('services.newsapi.sources', []);

        Artisan::call('fetch:news');

        Bus::assertNothingDispatched();
    }
}
