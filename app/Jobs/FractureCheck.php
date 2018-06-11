<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Traits\MoonExtractions;
use App\Traits\Tokens;
use App\Character;
use Log;

class FractureCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MoonExtractions, Tokens;

    protected $character;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Character $character)
    {
      $this->character = $character;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $refresh = $this->refreshToken($this->character->character_name);
      switch ($refresh) {
        case "not_expired":
          //Good to go
          break;

        case "refreshed":
          //Pull down new info from DB
          $this->character = Character::where('user_id', $this->character->user_id)->where('character_id', $this->character->character_id)->first();
          Log::debug("Refreshed token for {$this->character->character_name}");
          break;

        default:
          Log::error("Refresh function returned '$refresh' for {$this->character->character_name}");
          return;
          break;
      }

      $this->getExtractions($this->character);
    }
}
