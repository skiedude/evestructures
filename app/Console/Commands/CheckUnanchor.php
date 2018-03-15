<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UnanchorCheck;
use App\UnanchorNotice;
use App\Structure;
use App\NotificationManager;
use App\Notifications\UnanchorDiscord;

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
          if(!isset($notification->unanchor_webhook) || is_null($notification->unanchor_webhook)) {
            //Make sure we are always in a reset state if no webhooks are there
            UnanchorNotice::updateOrCreate(
              ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
              ['start_notice' => FALSE, 'finish_notice' => FALSE]
            );
            Log::debug("Ending UnanchorCheck for $structure->structure_name for $character->character_name, no unanchor_webhook");
            continue;
          }
          if($structure->unanchors_at == 'n/a') {
            UnanchorNotice::updateOrCreate(
              ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
              ['start_notice' => FALSE, 'finish_notice' => FALSE]
            );
            Log::debug("Ending FuelCheck for $structure->structure_name for $character->character_name, not unanchoring");
            continue;
          }

          $unanchors_at = $structure->unanchors_at;
          $unanchors_at_datetime = new \DateTime($structure->unanchors_at);
          $now = new \DateTime();
          $diff = date_diff($now,$unanchors_at_datetime);

          $unanchor_notices = UnanchorNotice::where('structure_id', $structure->structure_id)
                                    ->where('character_id', $character->character_id)->first();

          if(!isset($unanchor_notices->start_notice) || $unanchor_notices->start_notice == NULL) {
            Log::debug("Sending First Notice for Unanchoring of $structure->structure_name owned by $character->character_name");
            UnanchorNotice::updateOrCreate(
              ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
              ['start_notice' => TRUE, 'finish_notice' => FALSE]
            );

            $notification->notify(new UnanchorDiscord($structure, $character, 'START'));
          }

          if($diff->days == 0 && $diff->h <= 24) {
            if(!isset($unanchor_notices->finish_notice) || $unanchor_notices->finish_notice == NULL) {
              Log::debug("Sending Final Notice for Unanchoring of $structure->structure_name owned by $character->character_name");
              UnanchorNotice::updateOrCreate(
                ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
                ['start_notice' => TRUE, 'finish_notice' => TRUE]
              );

              $notification->notify(new UnanchorDiscord($structure, $character, 'FINISH'));
            }
          }

          Log::debug("Ending UnanchorCheck for $structure->structure_name for $character->character_name");

        }
      }
    }
}
