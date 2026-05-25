<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'description',
        'public',
        'collaborative',
        'hidden',
        'default'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function questions(){
        return $this->belongsToMany(Question::class);
    }

    public function reports(){
        return $this->morphMany(Report::class, 'reportable');
    }

    public function tags(){
        return $this->morphMany(Tag::class, 'taggable');
    }
}
