<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultItem extends Model
{
    protected $fillable = [
        'result_id',
        'question_id',
        'is_correct',
        'duration',
        'user_answer_content',
        'order'
    ];

    public function result(){
        return $this->belongsTo(Result::class, 'result_id');
    }

    public function question(){
        return $this->belongsTo(Question::class, 'question_id')->withTrashed();;
    }
}
