<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use \DiscordWebhooks\Client;
use \DiscordWebhooks\Embed;
use App\Channels\DiscordChannel;
use Log;

class ExtractionsDailyDiscord extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(\App\Character $character, $extractions)
    {
      $this->character = $character;
      $this->extractions = $extractions;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }


    public function toDiscord($notifiable) {
      try {
        $client = new Client($notifiable->extraction_webhook);
        $embed = new Embed();

        foreach($this->extractions as $ext) {
          $embed->field($ext->moon_name, "Chunk Arrives: $ext->chunk_arrival_time\nAuto Fracture: $ext->natural_decay_time");
        }

        $embed->color( 0x24d04a );
        $embed->title(":pick: **Upcoming 7 Day Extractions for {$this->character->corporation_name}** :pick:");
        $embed->author(env('APP_NAME'). 'Bot', null, "https://imageserver.eveonline.com/Character/{$notifiable->character_id}_64.jpg");

        $client->username(env('APP_NAME'))
                ->avatar(env('APP_URL') . "/images/avatar.png")
                ->embed($embed);

        return $client->send();
      } catch (\Exception $e) {
        Log::error("Failed to send ExtractionsDaily discord notification for {$this->character->character_name} on account $notifiable->user_id , $e");
      }
    }
}
