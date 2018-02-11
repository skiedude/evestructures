<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Character;
use App\Jobs\StructureUpdate;

class UpdateStructures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:structures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kick off a Job for each Character to update their structure data from ESI';

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
      $characters = Character::all();
      foreach($characters as $character) {
        StructureUpdate::dispatch($character);
      }
    }
}
