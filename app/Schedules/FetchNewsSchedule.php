<?php

namespace App\Schedules;

use Illuminate\Console\Scheduling\Schedule;

class FetchNewsSchedule
{
    public function __invoke(Schedule $schedule): void
    {
        $schedule->command('fetch:news')->hourly()->withoutOverlapping();
    }
}
