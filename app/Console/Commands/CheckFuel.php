<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\LowFuelCheck;
use App\FuelNotice;
use App\Structure;
use App\NotificationManager;
use Log;

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
      $structures = Structure::all();
      foreach ($structures as $structure) {
        $characters = Structure::find($structure->structure_id)->characters;
        foreach ($characters as $character) {
          Log::debug("Starting FuelCheck for {$structure->structure_name} owned by {$character->character_name}");

          $notification = NotificationManager::where('character_id', $character->character_id)->first();
          if(!isset($notification->discord_webhook) || is_null($notification->discord_webhook) || $notification->low_fuel == FALSE) {
            //If the fuel were to run out, we want to reset the notifications
            FuelNotice::updateOrCreate(
              ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
              ['seven_day' => FALSE, 'twentyfour_hour' => FALSE]
            );
            Log::debug("Ending FuelCheck for $structure->structure_name for $character->character_name, no discord_webhook or fuel alerts disabled");
            continue;
          }
          if($structure->fuel_expires == 'n/a') {
            FuelNotice::updateOrCreate(
              ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
              ['seven_day' => FALSE, 'twentyfour_hour' => FALSE]
            );
            Log::debug("Ending FuelCheck for $structure->structure_name for $character->character_name, no fuel");
            continue;
          }

          LowFuelCheck::dispatch($structure, $character, $notification);
        }
      }
    }
}
