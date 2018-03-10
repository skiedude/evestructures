<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Structure;
use App\Character;
use App\UnanchorNotice;
use App\NotificationManager;
use App\Notifications\UnanchorDiscord;
use Log;

class UnanchorCheck implements ShouldQueue
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

      $unanchors_at = $this->structure->unanchors_at;
      $unanchors_at_datetime = new \DateTime($this->structure->unanchors_at); 
      $now = new \DateTime();
      $diff = date_diff($now,$unanchors_at_datetime);

      $unanchor_notices = UnanchorNotice::where('structure_id', $this->structure->structure_id)
                                ->where('character_id', $this->character->character_id)->first();

      if(!isset($unanchor_notices->start_notice) || $unanchor_notices->start_notice == NULL) {
        Log::debug("Sending First Notice for Unanchoring of {$this->structure->structure_name} owned by {$this->character->character_name}");
        UnanchorNotice::updateOrCreate(
          ['structure_id' => $this->structure->structure_id, 'character_id' => $this->character->character_id],
          ['start_notice' => TRUE, 'finish_notice' => FALSE]
        ); 
       
        $this->notification->notify(new UnanchorDiscord($this->structure, $this->character, 'START')); 
      }
      if($diff->days <= 1) { 
        if(!isset($unanchor_notices->finish_notice) || $unanchor_notices->finish_notice == NULL) {
          Log::debug("Sending Final Notice for Unanchoring of {$this->structure->structure_name} owned by {$this->character->character_name}");
          UnanchorNotice::updateOrCreate(
            ['structure_id' => $this->structure->structure_id, 'character_id' => $this->character->character_id],
            ['start_notice' => TRUE, 'finish_notice' => TRUE]
          ); 
       
          $this->notification->notify(new UnanchorDiscord($this->structure, $this->character, 'FINISH')); 
        }
      }

      Log::debug("Ending UnanchorCheck for {$this->structure->structure_name} for {$this->character->character_name}");  
    }
}
