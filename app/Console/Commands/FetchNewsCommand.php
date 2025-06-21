<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsFetcherService;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'fetch:news')]
class FetchNewsCommand extends Command
{
    protected $description = 'Fetch news articles from multiple sources';

    public function handle()
    {
        $service = new NewsFetcherService();
        $count = $service->fetchAndStore();
        $this->info("Fetched and stored {$count} new articles.");
    }
}
