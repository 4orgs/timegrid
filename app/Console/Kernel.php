<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\AutopublishBusinessVacancies',
        'App\Console\Commands\SendRootReport',
        'App\Console\Commands\SendBusinessReport',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('root:report')->dailyAt(config('root.report.time'));

        $schedule->command('business:report')->dailyAt('21:00');

        $schedule->command('business:vacancies')->weekly()->sundays()->at('00:00');
    }
}
