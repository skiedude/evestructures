<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Structure;
use App\Character;
use App\NotificationManager;
use App\Notifications\ExtractionsDailyDiscord;
use Log;

class DailyExtractions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extraction:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for upcoming extractions';

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
        foreach ($characters as $character) {
          $extractions = array();
          Log::debug("Starting Daily Extractions Notification for {$character->character_name}");
          $structures = Character::find($character->character_id)->structures;
          foreach($structures as $structure) {
            $extraction = Structure::find($structure->structure_id)->extractions;
            if(!is_null($extraction)) {
              $fracture_time = new \DateTime($extraction->chunk_arrival_time);
              $now = new \DateTime();
              $diff = date_diff($now, $fracture_time);
              if ($diff->days <= 7 && $diff->invert == 0) {
                array_push($extractions, $extraction); 
              }
            }
          }
          
          if(count($extractions) > 0) {
            $notification = NotificationManager::where('character_id', $character->character_id)->first();
            if(!isset($notification->extraction_webhook) || is_null($notification->extraction_webhook)) {
              //Nothing to send withot a webhook
              Log::debug("Ending Daily Extractions Notification for $structure->structure_name for $character->character_name, no extraction_webhook");
              continue;
            }

            $notification->notify(new ExtractionsDailyDiscord($character, $extractions));
            Log::debug("Ending Daily Extractions Notification for $structure->structure_name for $character->character_name");
          }
        }
    }
}
