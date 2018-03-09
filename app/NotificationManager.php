<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class NotificationManager extends Model
{
  use Notifiable;

  protected $guarded = [];

  public function routeNotificationForDiscord() {
    $this->discord_webhook;
  }

}
