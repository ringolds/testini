<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Map extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'js_path',
        'svg_path'
    ];

    public function questionMaps(){
        return $this->hasMany(QuestionMap::class);
    }
}
