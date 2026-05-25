<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionImage extends Model
{
    protected $fillable = [
        'path',
        'alt_text',
        'mime_type',
        'size',
        'width',
        'height'
    ];

    public function questionComponent(){
        return $this->morphOne(questionComponent::class, 'component');
    }
}
