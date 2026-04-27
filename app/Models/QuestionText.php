<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionText extends Model
{
    protected $fillable = [
        'text',
    ];

    public function questionComponent(){
        return $this->morphOne(QuestionComponent::class, 'component');
    }
}
