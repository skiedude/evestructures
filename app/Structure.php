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

    public function services() {
      return $this->hasMany('App\StructureService', 'structure_id', 'structure_id');
    }

    public function states() {
      return $this->hasMany('App\StructureState', 'structure_id', 'structure_id');
    }

    public function vuls() {
      return $this->hasMany('App\StructureVul', 'structure_id', 'structure_id');
    }

}

