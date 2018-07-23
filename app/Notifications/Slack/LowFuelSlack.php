<?php

namespace App\Notifications\Slack;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Log;

class LowFuelSlack extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(\App\Structure $structure, \App\Character $character)
    {
      $this->structure = $structure;
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
        return ['slack'];
    }


    public function toSlack($notifiable) {

      try {
        return (new SlackMessage)
          ->warning()
          ->image(env('APP_URL') . "/images/avatar.png")
          ->content(":warning: *Fuel Alert* for {$this->character->corporation_name} :warning:")
          ->from(env('APP_NAME') . 'Bot')
          ->attachment(function ($attachment) {
            $attachment->title("{$this->structure->structure_name}", env('APP_URL') . "/home/structure/{$this->structure->structure_id}")
              ->thumb("https://imageserver.eveonline.com/Type/{$this->structure->type_id}_64.png")
              ->fields([
              'Fuel Remaining' => $this->structure->fuel_time_left,
              'Fuel Expiration' => $this->structure->fuel_expires,
              'System' => $this->structure->system_name,
            ]);
          });
      } catch (\Exception $e) {
        Log::error("Failed to send LowFuel slack notification for {$this->character->character_name} on account $notifiable->user_id , $e");
      }
    }
}
