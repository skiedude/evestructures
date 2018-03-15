<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\FuelNotice;
use App\Structure;
use App\NotificationManager;
use App\Notifications\LowFuelDiscord;
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
          Log::debug("Starting FuelCheck for $structure->structure_name owned by $character->character_name");

          $notification = NotificationManager::where('character_id', $character->character_id)->first();
          if(!isset($notification->fuel_webhook) || is_null($notification->fuel_webhook)) {
            //If the fuel were to run out, we want to reset the notifications
            FuelNotice::updateOrCreate(
              ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
              ['seven_day' => FALSE, 'twentyfour_hour' => FALSE]
            );
            Log::debug("Ending FuelCheck for $structure->structure_name for $character->character_name, no fuel_webhook");
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

          $days_left = $structure->fuel_days_left;
          $fuel_notices = FuelNotice::where('structure_id', $structure->structure_id)
                                    ->where('character_id', $character->character_id)->first();

          if($days_left < 1) {
            if((isset($fuel_notices) && $fuel_notices->twentyfour_hour == FALSE ) || !isset($fuel_notices->twentyfour_hour)) {
              //Notify for <24hrs fuel if we haven't already, mark it notified
              Log::debug("Sending 24hour notice (days left are $days_left) for $structure->structure_name owned by $character->character_name");
              FuelNotice::updateOrCreate(
                ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
                ['twentyfour_hour' => TRUE]);
              $notification->notify(new LowFuelDiscord($structure, $character));
            }
          } elseif($days_left > 0 && $days_left <= 7) {
              if((isset($fuel_notices) && $fuel_notices->seven_day == FALSE) || !isset($fuel_notices->seven_day)) {
                //Notify if more than 1 day and less than 7 if we haven't already, mark it notified
                Log::debug("Sending 7day notice (days left are $days_left) for $structure->structure_name owned by $character->character_name");
                FuelNotice::updateOrCreate(
                  ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
                  ['seven_day' => TRUE, 'twentyfour_hour' => FALSE]
                );
                $notification->notify(new LowFuelDiscord($structure, $character));
            } elseif(isset($fuel_notices)) {
                //If we are above 1 day, but less than 7 and we HAVE notified then mark the 24hour as not sent
                // This catches people who only refuel for less than a week that we already notified for 24 hours
                FuelNotice::updateOrCreate(
                  ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
                  ['seven_day' => TRUE, 'twentyfour_hour' => FALSE]
                );
            }
          } elseif($days_left > 7) {
              //reset all the notifications, we can do them all again
              FuelNotice::updateOrCreate(
                ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
                ['seven_day' => FALSE, 'twentyfour_hour' => FALSE]
              );

          } else {
            //Nothing
          }
          Log::debug("Ending FuelCheck for $structure->structure_name for $character->character_name");
        }
      }
    }
}
