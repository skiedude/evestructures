<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UnanchorCheck;
use App\UnanchorNotice;
use App\Structure;
use App\NotificationManager;
use Log;

class CheckUnanchor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:unanchor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the Unanchor check and send notifications if required.';

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
      $structures = Structure::all();
      foreach ($structures as $structure) {
        $characters = Structure::find($structure->structure_id)->characters;
        foreach ($characters as $character) {
          Log::debug("Starting UnanchorCheck for {$structure->structure_name} owned by {$character->character_name}");

          $notification = NotificationManager::where('character_id', $character->character_id)->first();
          if(!isset($notification->discord_webhook) || is_null($notification->discord_webhook) || $notification->unanchor == FALSE) {
            //Make sure we are always in a reset state if no webhooks are there
            UnanchorNotice::updateOrCreate(
              ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
              ['start_notice' => FALSE, 'finish_notice' => FALSE]
            );
            Log::debug("Ending UnanchorCheck for $structure->structure_name for $character->character_name, no discord_webhook or unanchor alerts disabled");
            continue;
          }
          if($structure->unanchors_at == 'n/a') {
            UnanchorNotice::updateOrCreate(
              ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
              ['seven_day' => FALSE, 'twentyfour_hour' => FALSE]
            );
            Log::debug("Ending FuelCheck for $structure->structure_name for $character->character_name, not unanchoring");
            continue;
          }

          UnanchorCheck::dispatch($structure, $character, $notification);
        }
      }
    }
}
