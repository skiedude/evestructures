<?php

namespace App\Notifications\Discord;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use \DiscordWebhooks\Client;
use \DiscordWebhooks\Embed;
use App\Channels\DiscordChannel;
use Log;

class UnanchorDiscord extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(\App\Structure $structure, \App\Character $character, $unotice)
    {
      $this->structure = $structure;
      $this->character = $character;
      $this->unotice = $unotice;
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
        $client = new Client($notifiable->unanchor_webhook);

        $embed = new Embed();

        if($this->unotice == 'START') {
          $embed->color( 0xf0ba3c );
          $embed->description(":anchor: **START Unanchor Alert** for {$this->character->corporation_name} :anchor:");
        } else {
          $embed->color( 0xff2d32 );
          $embed->description(":anchor: **FINAL Unanchor Alert** for {$this->character->corporation_name}:anchor:");
        }

        $embed->title("{$this->structure->structure_name}", env('APP_URL') . "/home/structure/{$this->structure->structure_id}");
        $embed->thumbnail("https://imageserver.eveonline.com/Type/{$this->structure->type_id}_64.png");
        $embed->author(env('APP_NAME'). 'Bot', null, "https://imageserver.eveonline.com/Character/{$notifiable->character_id}_64.jpg");
        $embed->field('Unanchors At', $this->structure->unanchors_at, TRUE);
        $embed->field('System', $this->structure->system_name, TRUE);

        $client->username(env('APP_NAME'))
                ->avatar(env('APP_URL') . "/images/avatar.png")
                ->embed($embed);

        return $client->send();
      } catch (\Exception $e) {
        Log::error("Failed to send unanchor discord notification for {$this->character->character_name} on account $notifiable->user_id , $e");
      }
    }
}
