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
}
