<?php

namespace App\Notifications\Slack;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Log;

class UnanchorSlack extends Notification
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
        return ['slack'];
    }


    public function toSlack($notifiable) {
      try {
        return (new SlackMessage)
          ->warning()
          ->image(env('APP_URL') . "/images/avatar.png")
          ->content(":anchor: *$this->unotice Unanchor Alert* for {$this->character->corporation_name} :anchor:")
          ->from(env('APP_NAME') . 'Bot')
          ->attachment(function ($attachment) {
            $attachment->title("{$this->structure->structure_name}", env('APP_URL') . "/home/structure/{$this->structure->structure_id}")
              ->thumb("https://imageserver.eveonline.com/Type/{$this->structure->type_id}_64.png")
              ->fields([
              'Unanchors At' => $this->structure->unanchors_at,
              'System' => $this->structure->system_name,
            ]);
          });
      } catch (\Exception $e) {
        Log::error("Failed to send unanchor slack notification for {$this->character->character_name} on account $notifiable->user_id , $e");
      }
    }
}
