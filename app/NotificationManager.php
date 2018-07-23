<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class NotificationManager extends Model
{
  use Notifiable;

  protected $guarded = [];
  protected $table = 'notification_info';

  public $slack_webhook;

  public function routeNotificationForSlack() {
    return $this->{$this->slack_webhook};
  }

  public function slackChannel($channel) {
    $this->slack_webhook = $channel;
    return $this;
  }
}
