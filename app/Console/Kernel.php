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
        Commands\ClassifiedListingExpiration::class,
        Commands\BumpAdsListingUpdatedAtDate::class,
        Commands\FeatureListingUpdateAtDate::class,
        Commands\CloseAuction::class,
        Commands\AutoBid::class,
        Commands\HighlightListingUpdateAtDate::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('auction:close')->everyTwoMinutes();
        /** 
         * Note: Disabling autobid since this will only run when someone place bid
         * Keeping this for future, incase we need to run this again and again
        */
        // $schedule->command('auto:bid')->everyMinute();

        $schedule->command('featurelistingmove:top')->everyMinute();
        $schedule->command('bump:ads')->dailyAt('10:00');
        $schedule->command('classifiedlisting:expiration')->dailyAt('11:00');
        $schedule->command('highlightlistingmove:top')->dailyAt('12:00');

        $schedule->command('fpx:banks')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
