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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
      $schedule->command('check:orphans')->weekly();
      $schedule->command('update:structures')->hourly();
      $schedule->command('check:unanchor')->hourlyAt(15);
      $schedule->command('check:fuel')->hourlyAt(30);
      $schedule->command('check:fracture')->hourlyAt(45);
      $schedule->command('extraction:daily')->dailyAt('13:00');
      $schedule->command('update:character')->twiceDaily(1,14);
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
