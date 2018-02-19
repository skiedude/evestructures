<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    protected $guarded = [];
    public $incrementing = false;
    protected $primaryKey = 'structure_id';


    public function characters() {
      return $this->hasMany('App\Character', 'corporation_id', 'corporation_id');
    }
}
