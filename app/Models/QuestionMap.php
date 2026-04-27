<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionMap extends Model
{
    protected $fillable = [
        'map_id',
        'target_region',
    ];

    public function map(){
        return $this->belongsTo(Map::class, 'map_id');
    }

    public function questionComponent(){
        return $this->morphOne(QuestionComponent::class, 'component');
    }
}
