<?php

namespace App\Notifications\Slack;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Log;

class ExtractionsDailySlack extends Notification
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
        return ['slack'];
    }


    public function toSlack($notifiable) {
      try {
        $extractions = ""; 
        foreach($this->extractions as $ext) {
          $extractions .= "*$ext->moon_name*\nChunk Arrives: $ext->chunk_arrival_time\nAuto Fracture: $ext->natural_decay_time\n";
        }

        Log::debug(print_r($extractions, true));
        return (new SlackMessage)
          ->success()
          ->image(env('APP_URL') . "/images/avatar.png")
          ->content(":pick: *Upcoming 7 Day Extractions for {$this->character->corporation_name}* :pick:")
          ->from(env('APP_NAME') . 'Bot')
          ->attachment(function ($attachment) use ($extractions) {
            $attachment->fields(['' => $extractions]);
          });

      } catch (\Exception $e) {
        Log::error("Failed to send ExtractionsDaily slack notification for {$this->character->character_name} on account $notifiable->user_id , $e");
      }
    }
}
