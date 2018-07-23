<?php

namespace App\Notifications\Slack;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Log;

class testSlack extends Notification
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
        return ['slack'];
    }


    public function toSlack($notifiable) {
      try {
        Log::debug("Sent Test Slack notification for {$this->character->character_name} for webhook $this->webhook");
        return (new SlackMessage)
          ->success()
          ->image(env('APP_URL') . "/images/avatar.png")
          ->content(':heavy_check_mark: *Webhook test* :heavy_check_mark:')
          ->from(env('APP_NAME') . 'Bot')
          ->attachment(function ($attachment) {
            $attachment->title("Webhook Test")
            ->content("Webhook test for {$this->character->character_name} for $this->webhook");
          });
      } catch (\Exception $e) {
        Log::error("Failed to send <TEST> Slack notification for {$this->character->character_name} on account $notifiable->user_id , $e");
      }
    }
}
