<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\StructureUpdate;
use App\Jobs\LowFuelCheck;
use App\Character;

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
        // $schedule->command('inspire')
        //          ->hourly();
				try {
					$characters = Character::all();
					foreach($characters as $character) {
						//Run Every 3 Hours
						$schedule->job(new StructureUpdate($character))->cron('0 */3 * * *');
					}
				} catch (\Exception $e){
					//Catch new migrate commands when characters table doesn't exist yet
				}

				$schedule->job(new LowFuelCheck())->hourly();
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
