<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Extractions extends Model
{
    protected $guarded = [];
    public $incrementing = false;
    protected $primaryKey = 'structure_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'extraction_start_time', 'chunk_arrival_time', 'natural_decay_time'];
}
