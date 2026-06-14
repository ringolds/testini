<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'user_id',
        'test_id',
        'stars',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function test(){
        return $this->belongsTo(Test::class, 'test_id');
    }
}
