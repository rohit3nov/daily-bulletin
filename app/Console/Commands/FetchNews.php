<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use App\Services\ArticleService;

#[AsCommand(name: 'fetch:news')]
class FetchNews extends Command
{
    protected $signature = 'fetch:news';
    protected $description = 'Dispatch jobs to fetch articles for all sources and categories';

    public function handle(): int
    {
        foreach (array_keys(config('services.newsapi.sources', [])) as $class) {
            foreach (config('services.newsapi.categories', []) as $category) {
                \App\Jobs\FetchNews::dispatch(
                    app("App\\Services\\NewsApi\\{$class}"),
                    $category,
                    app(ArticleService::class)
                );
            }
        }

        $this->info('Dispatched fetch jobs for all sources and categories.');

        return Command::SUCCESS;
    }
}
