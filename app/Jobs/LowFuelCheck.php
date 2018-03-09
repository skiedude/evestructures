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
use App\NotificationManager;
use App\Notifications\LowFuelDiscord;
use Log;

class LowFuelCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $structure;
    protected $character;
    protected $notification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Structure $structure, Character $character, NotificationManager $notification)
    {
      $this->structure = $structure;
      $this->character = $character;
      $this->notification = $notification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

      $days_left = $this->structure->fuel_days_left;
      $fuel_notices = FuelNotice::where('structure_id', $this->structure->structure_id)
                                ->where('character_id', $this->character->character_id)->first();

      if($days_left < 1) {
        if((isset($fuel_notices) && $fuel_notices->twentyfour_hour == FALSE ) || !isset($fuel_notices->twentyfour_hour)) {
          //Notify for <24hrs fuel if we haven't already, mark it notified
          Log::debug("Sending 24hour notice (days left are $days_left) for {$this->structure->structure_name} owned by {$this->character->character_name}"); 
          FuelNotice::updateOrCreate(
            ['structure_id' => $this->structure->structure_id, 'character_id' => $this->character->character_id],
            ['twentyfour_hour' => TRUE]); 
          $this->notification->notify(new LowFuelDiscord($this->structure, $this->character)); 
        }
      } elseif($days_left > 0 && $days_left <= 7) {
          if((isset($fuel_notices) && $fuel_notices->seven_day == FALSE) || !isset($fuel_notices->seven_day)) {
            //Notify if more than 1 day and less than 7 if we haven't already, mark it notified
            Log::debug("Sending 7day notice (days left are $days_left) for {$this->structure->structure_name} owned by {$this->character->character_name}"); 
            FuelNotice::updateOrCreate(
              ['structure_id' => $this->structure->structure_id, 'character_id' => $this->character->character_id],
              ['seven_day' => TRUE, 'twentyfour_hour' => FALSE]
            ); 
            $this->notification->notify(new LowFuelDiscord($this->structure, $this->character)); 
        } elseif(isset($fuel_notices)) {
            //If we are above 1 day, but less than 7 and we HAVE notified then mark the 24hour as not sent
            // This catches people who only refuel for less than a week that we already notified for 24 hours
            FuelNotice::updateOrCreate(
              ['structure_id' => $this->structure->structure_id, 'character_id' => $this->character->character_id],
              ['seven_day' => TRUE, 'twentyfour_hour' => FALSE]
            ); 
        }
      } elseif($days_left > 7) {
          //reset all the notifications, we can do them all again
          FuelNotice::updateOrCreate(
            ['structure_id' => $this->structure->structure_id, 'character_id' => $this->character->character_id],
            ['seven_day' => FALSE, 'twentyfour_hour' => FALSE]
          ); 
      
      } else {
        //Nothing
      }
      Log::debug("Ending FuelCheck for {$this->structure->structure_name} for {$this->character->character_name}");  
    }
}
