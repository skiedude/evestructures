<?php

namespace App\Notifications\Discord;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use \DiscordWebhooks\Client;
use \DiscordWebhooks\Embed;
use App\Channels\DiscordChannel;
use Log;

class StrctStateChange extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(\App\Structure $structure, \App\Character $character, $old_state, $new_state)
    {
      $this->structure = $structure;
      $this->character = $character;
      $this->old_state = $old_state;
      $this->new_state = $new_state;
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

        if(stripos($this->old_state, 'anchor') !== false || stripos($this->new_state, 'anchor') !== false) {
          //Keep anchoring things together
          $webhook = $notifiable->unanchor_webhook;
        } else {
          $webhook = $notifiable->state_webhook;
        }

        $client = new Client($webhook);
        $embed = new Embed();

        if($this->new_state == 'shield_vulnerable') {
          $embed->color( 0x24d04a );
        } else {
           $embed->color( 0xff2d32 );
        }
        $embed->description(":no_entry: **Structure Changed State** for {$this->character->corporation_name} :no_entry:");
        $embed->title("{$this->structure->structure_name}", env('APP_URL') . "/home/structure/{$this->structure->structure_id}");
        $embed->thumbnail("https://imageserver.eveonline.com/Type/{$this->structure->type_id}_64.png");
        $embed->author(env('APP_NAME'). 'Bot', null, "https://imageserver.eveonline.com/Character/{$notifiable->character_id}_64.jpg");
        $embed->field('Old State', $this->old_state, TRUE);
        $embed->field('New State', $this->new_state, TRUE);
        $embed->field('System', $this->structure->system_name, TRUE);

        $client->username(env('APP_NAME'))
                ->avatar(env('APP_URL') . "/images/avatar.png")
                ->embed($embed);

        return $client->send();
      } catch (\Exception $e) {
        Log::error("Failed to send structure state change discord notification for {$this->character->character_name} on account $notifiable->user_id , $e");
      }
    }
}
