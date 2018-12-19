<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;
use App\Character;
use App\Slug;
use Log;

class CharacterUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
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
      Log::debug("Updating Public info for {$this->character->character_name}");
      try {
        $client = new Client();
        $character_url = config('app.CCP_URL') . "/v4/characters/{$this->character->character_id}";
        $noauth_headers = [
          'headers' => [
            'User-Agent' => env('USERAGENT'),
          ],
        ];
        $resp = $client->get($character_url, $noauth_headers);
        $updated_character = json_decode($resp->getBody());

        $corp_url = config('app.CCP_URL') . "/v4/corporations/$updated_character->corporation_id";
        $resp = $client->get($corp_url, $noauth_headers);
        $corp = json_decode($resp->getBody());

        Character::updateOrCreate(
          ['character_id' => $this->character->character_id],
          ['corporation_id' => $updated_character->corporation_id,
           'corporation_name' => $corp->name,]
        );

        Slug::updateOrCreate(
          ['character_id' => $this->character->character_id],
          ['corporation_id' => $updated_character->corporation_id]
        );
        Log::debug("Finished Updating Public info for {$this->character->character_name}");
        return;
      } catch (\Exception $e) {
        Log::error("Exception caught on public data for {$this->character->character_name}: " . $e->getMessage());
        return;
      } 
    }
}
