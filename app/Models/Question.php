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
        return $this->components()
            ->where('role', 'question')
            ->orderBy('order')
            ->with('component')
            ->get();
    }

    public function answer(){
        return $this->components()
            ->where('role', 'answer')
            ->orderBy('order')
            ->with('component')
            ->get();
    }

    public function reports(){
        return $this->morphMany(Report::class, 'reportable');
    }

    public function tags(){
        return $this->morphMany(Tag::class, 'taggable');
    }
}
