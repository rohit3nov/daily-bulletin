<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'fetch:news')]
class FetchNews extends Command
{
    protected $signature = 'fetch:news';
    protected $description = 'Dispatch jobs to fetch articles for all sources and categories';

    public function handle(): int
    {
        foreach (config('services.newsapi.sources', []) as $source => $config) {
            foreach ($config["categories"] as $category) {
                \App\Jobs\FetchNews::dispatch(
                    $source,
                    $category
                );
            }
        }

        $this->info('Dispatched fetch jobs for all sources and categories.');

        return Command::SUCCESS;
    }
}
