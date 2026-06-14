<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [
        'name',
        'description',
        'public',
        'hidden',
        'user_id',
    ];

    protected $casts = [
        'public' => 'boolean',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ratings(){
        return $this->hasMany(Rating::class);
    }

    public function results(){
        return $this->hasMany(Result::class);
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

    public function banks(){
        return $this->belongsToMany(Bank::class, 'bank_test')
                    ->withPivot('random_count')
                    ->withTimestamps();
    }

    public function individualRating(int $userId){
        return (int) $this->ratings()
            ->where('user_id', $userId)
            ->value('stars');
    }

    public function averageRating(){
        return $this->ratings()->avg('stars');
    }
}
