<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
  use Notifiable;

  protected $guarded = [];
  public $incrementing = false;
  protected $primaryKey = 'character_id';

  public function corporation_id() {
    return $this->corporation_id;
  }
   
  public function structures() {
    return $this->hasMany('App\Structure', 'corporation_id', 'corporation_id');
  }
}
