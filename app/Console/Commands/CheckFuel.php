<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\LowFuelCheck;

class CheckFuel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:fuel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the Fuel check and send notifications if required.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      LowFuelCheck::dispatch();
    }
}
