<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    protected $fillable = [
        'name',
        'svh_path',
        'js_config_path',
    ];

    public function questionMaps(){
        return $this->hasMany(QuestionMap::class);
    }
}
