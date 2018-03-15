<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\StrctStateChange;
use App\Structure;
use App\NotificationManager;
use Log;

class StructureStateChange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strct:state {structure_id} {old_state} {new_state}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification for Structure State Change';

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
      $structure = Structure::find($this->argument('structure_id'));
      $characters = Structure::find($structure->structure_id)->characters;
      foreach ($characters as $character) {
        Log::debug("Starting Structure State Change Notification for {$structure->structure_name} owned by {$character->character_name}");

        $notification = NotificationManager::where('character_id', $character->character_id)->first();
        if(!isset($notification->state_webhook) || is_null($notification->state_webhook)) {
          Log::debug("Ending Structure State Change Notification for $structure->structure_name for $character->character_name, no state_webhook");
          continue;
        }

        $notification->notify(new StrctStateChange($structure, $character, $this->argument('old_state'), $this->argument('new_state')));
         Log::debug("Ending Structure State Change Notification for $structure->structure_name for $character->character_name, notification successfully sent");
      }
    }
}
