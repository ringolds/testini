<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'user_id'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function banks(){
        return $this->belongsToMany(Bank::class);
    }

    public function tests(){
        return $this->belongsToMany(Test::class);
    }

    public function components(){
        return $this->hasMany(QuestionComponent::class);
    }

    public function prompt(){
        return $this->hasOne(QuestionComponent::class)->where('role', 'question');
    }

    public function answer(){
        return $this->hasOne(QuestionComponent::class)->where('role', 'answer');
    }

    public function description(){
        return $this->hasOne(QuestionComponent::class)->where('role', 'description');
    }

    public function reports(){
        return $this->morphMany(Report::class, 'reportable');
    }

    public function tags(){
        return $this->morphMany(Tag::class, 'taggable');
    }

    public function resultItems(){
        return $this->hasMany(ResultItems::class, 'question_id');
    }
}
