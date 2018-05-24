<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use \DiscordWebhooks\Client;
use \DiscordWebhooks\Embed;
use App\Channels\DiscordChannel;
use Log;

class FractureDiscord extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($type, $extraction, $moon, $extraction_data, $character)
    {
      $this->type = $type;
      $this->extraction = $extraction;
      $this->moon = $moon;
      $this->extraction_data = $extraction_data;
      $this->character = $character;
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

        if($this->type == 'manual') {
          $embed->field("Manual Fracture", $this->extraction->chunk_arrival_time);
          $embed->description(":boom: {$this->moon->name} is ready to Manual Fracture for {$this->character->corporation_name}! :boom:");
        } else {
          $embed->field("Auto Fracture", $this->extraction->natural_decay_time);
          $embed->description(":boom: {$this->moon->name} is about to Auto Fracture for {$this->character->corporation_name}! :boom:");
        }

        $embed->field("Moon", $this->moon->name);
        $embed->field("Ores Available", $this->extraction_data->ores);
        $embed->field("Estimated Value", $this->extraction_data->value);
        $embed->color( 0x24d04a );
        $embed->author(env('APP_NAME'). 'Bot', null, "https://imageserver.eveonline.com/Character/{$notifiable->character_id}_64.jpg");

        $client->username(env('APP_NAME'))
                ->avatar(env('APP_URL') . "/images/avatar.png")
                ->embed($embed);

        Log::debug("Sending Fracture discord notification for {$this->moon->name} for character $notifiable->character_id");
        return $client->send();
      } catch (\Exception $e) {
        Log::error("Failed to send fracture discord notification for {$this->moon->name} on account $notifiable->user_id , $e");
      }
    }
}
