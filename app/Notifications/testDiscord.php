<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use \DiscordWebhooks\Client;
use \DiscordWebhooks\Embed;
use App\Channels\DiscordChannel;
use Log;

class testDiscord extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(\App\Character $character, $webhook)
    {
      $this->character = $character;
      $this->webhook = $webhook;
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
        $client = new Client($notifiable->{$this->webhook});

        $embed = new Embed();
        $embed->description(':white_check_mark: **Webhook Test** :white_check_mark:');
        $embed->title("Test Notification for {$this->character->character_name}");
        $embed->color( 0x0000a0 );
        $embed->author(env('APP_NAME'). 'Bot', null, "https://imageserver.eveonline.com/Character/{$notifiable->character_id}_64.jpg");
        $embed->field('Test Message', 'Ahoy Champion', TRUE);
        $embed->field('Webhook', "$this->webhook", TRUE);

        $client->username(env('APP_NAME'))
                ->avatar(env('APP_URL') . "/images/avatar.png")
                ->embed($embed);
        Log::debug("Sent Test Discord notification for {$this->character->character_name} for webhook $this->webhook");
        return $client->send();
      } catch (\Exception $e) {
        Log::error("Failed to send <TEST> discord notification for {$this->character->character_name} on account $notifiable->user_id , $e");
      }
    }
}
