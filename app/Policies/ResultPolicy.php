<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ResultItem;
use App\Models\Result;
use Illuminate\Auth\Access\Response;

class ResultPolicy
{
    public function getQuestion(User $user, Result $result, ResultItem $resultItem):bool
    {
        return $result->user_id == $user->id && $resultItem->result_id == $result->id;
    }

    public function submitQuestion(User $user, Result $result, ResultItem $resultItem):bool
    {
        return $result->user_id == $user->id && 
        $resultItem->result_id == $result->id &&
        $result->end_time == null &&
        $resultItem->is_correct == null;
    }

    public function getSummary(User $user, Result $result):bool
    {
        return $result->user_id == $user->id && $result->end_time != null;
    }
}
