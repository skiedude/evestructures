<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use \DiscordWebhooks\Client;
use \DiscordWebhooks\Embed;
use App\Channels\DiscordChannel;
use Log;

class LowFuelDiscord extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(\App\Structure $structure)
    {
      $this->structure = $structure;
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
        $client = new Client($notifiable->discord_webhook);

        $embed = new Embed();
        $embed->title("{$this->structure->structure_name}", env('APP_URL') . "/home/structure/{$this->structure->structure_id}");
        $embed->description(':warning: **Fuel Alert** :warning:');
        $embed->color( 15105570 );
        $embed->thumbnail("https://imageserver.eveonline.com/Type/{$this->structure->type_id}_64.png");
        $embed->author(env('APP_NAME'). 'Bot', null, "https://imageserver.eveonline.com/Character/{$notifiable->character_id}_64.jpg");
        $embed->field('Fuel Remaining', $this->structure->fuel_time_left, TRUE);
        $embed->field('Fuel Expiration', $this->structure->fuel_expires, TRUE);
        $embed->field('System', $this->structure->system_name, TRUE);

        $client->username(env('APP_NAME'))
                ->avatar(env('APP_URL') . "/images/avatar.png")
                ->embed($embed);

        return $client->send();
      } catch (\Exception $e) {
        Log::error("Failed to send discord notification for $notifiable->character_name on account $notificable->user_id , $e->getMessage()");
      }
    }
}
