<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class DiscordChannel {

  public function send($notifiable, Notification $notification) {
    $message = $notification->toDiscord($notifiable); 
  }

}
