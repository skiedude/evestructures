<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\OrphanStructure;

class CheckOrphans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:orphans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup orphaned structures from deleted characters and accounts';

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
      OrphanStructure::dispatch();
    }
}
