<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\NewsApiInterface;
use App\Services\NewsApi\NewsOrg;
use App\Services\NewsApi\Guardian;
use App\Services\NewsApi\NYTimes;
use Nette\InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NewsApiInterface::class, function ($app, $params) {
            $map = [
                'newsorg'  => NewsOrg::class,
                'guardian' => Guardian::class,
                'nytimes'  => NYTimes::class,
            ];

            $source = $params['source'] ?? null;

            if (!isset($map[$source])) {
                throw new InvalidArgumentException("Unsupported news source: {$source}");
            }

            return new $map[$source]();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
