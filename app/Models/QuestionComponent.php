<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionComponent extends Model
{
    protected $fillable = [
        'question_id',
        'component_id',
        'component_type',
        'order',
        'role',
    ];

    public function question(){
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function component(){
        return $this->morphTo();
    }
}   
