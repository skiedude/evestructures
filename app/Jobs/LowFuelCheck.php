<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Structure;
use App\Character;
use App\FuelNotice;
use App\Notifications\LowFuelDiscord;
use Log;

class LowFuelCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $structures = Structure::all();
      foreach ($structures as $structure) {
        $characters = Structure::find($structure->structure_id)->characters;
        foreach ($characters as $character) {
          Log::debug("Starting FuelCheck for $structure->structure_name owned by $character->character_name"); 

          if(!isset($character->discord_webhook) || is_null($character->discord_webhook)) {
            //If the fuel were to run out, we want to reset the notifications
            FuelNotice::updateOrCreate(
              ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
              ['seven_day' => FALSE, 'twentyfour_hour' => FALSE]
            ); 
            Log::debug("Ending FuelCheck for $structure->structure_name for $character->character_name, no discord_webhook"); 
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
              $character->notify(new LowFuelDiscord($structure)); 
            }
          } elseif($days_left > 0 && $days_left <= 7) {
              if((isset($fuel_notices) && $fuel_notices->seven_day == FALSE) || !isset($fuel_notices->seven_day)) {
                //Notify if more than 1 day and less than 7 if we haven't already, mark it notified
                Log::debug("Sending 7day notice (days left are $days_left) for $structure->structure_name owned by $character->character_name"); 
                FuelNotice::updateOrCreate(
                  ['structure_id' => $structure->structure_id, 'character_id' => $character->character_id],
                  ['seven_day' => TRUE, 'twentyfour_hour' => FALSE]
                ); 
                $character->notify(new LowFuelDiscord($structure)); 
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
