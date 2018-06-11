<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Character;
use App\Jobs\FractureCheck;

class Fracture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:fracture {character_name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for upcoming/happening Moon Chunk Fractures';

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
      if($this->argument('character_name')) {
        $character = Character::where('character_name', $this->argument('character_name'))->first();
        if(is_null($character)) {
          Log::error("No character found by name {$this->argument('character_name')}");
          return;
        }

        FractureCheck::dispatch($character);

      } else {
        $characters = Character::all();
        foreach ($characters as $character) {
          if($character->is_manager) {
            FractureCheck::dispatch($character);
          }
        }
      }

    }
}
