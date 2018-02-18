<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
  use Notifiable;

  protected $guarded = [];

  public function routeNotificationForDiscord() {
    $this->discord_webhook;
  }

  public function corporation_id() {
    return $this->corporation_id;
  }
   
  public function structures() {
    return $this->hasMany(\App\Structures, 'corporation_id', 'corporation_id');
  }
}
