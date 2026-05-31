<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    protected $fillable = [
        'name',
        'js_path',
        'svg_path'
    ];

    public function questionMaps(){
        return $this->hasMany(QuestionMap::class);
    }
}
