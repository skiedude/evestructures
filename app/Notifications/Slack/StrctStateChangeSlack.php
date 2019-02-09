<?php

namespace App\Notifications\Slack;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Log;

class StrctStateChangeSlack extends Notification
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
        return ['slack'];
    }


    public function toSlack($notifiable) {

      $ping_here = $notifiable->state_ping_here == True ? ' <!here>' : '';
      try {
        return (new SlackMessage)
          ->image(env('APP_URL') . "/images/avatar.png")
          ->content(":large_orange_diamond: *Structure Changed State* for {$this->character->corporation_name} :large_orange_diamond: $ping_here")
          ->from(env('APP_NAME') . 'Bot')
          ->attachment(function ($attachment) {
            $attachment->title("{$this->structure->structure_name}", env('APP_URL') . "/home/structure/{$this->structure->structure_id}")
              ->thumb("https://imageserver.eveonline.com/Type/{$this->structure->type_id}_64.png")
              ->fields([
              'Old State' => $this->old_state,
              'New State' => $this->new_state,
              'System' => $this->structure->system_name,
            ]);
          });

      } catch (\Exception $e) {
        Log::error("Failed to send structure state change slack notification for {$this->character->character_name} on account $notifiable->user_id , $e");
      }
    }
}
