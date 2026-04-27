<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'score',
        'user_id',
        'test_id',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function test(){
        return $this->belongsTo(Test::class, 'test_id');
    }

    public function items(){
        return $this->hasMany(ResultItems::class);
    }
}
