<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Character;
use App\Jobs\CharacterUpdate;

class UpdateCharacter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:character {character_name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Characters Corporation Information';

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

        CharacterUpdate::dispatch($character);

      } else {
        $characters = Character::all();
        foreach ($characters as $character) {
          CharacterUpdate::dispatch($character);
        }
      }

    }
}
