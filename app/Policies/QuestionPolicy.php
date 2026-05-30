<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;
use App\Models\Bank;
use Illuminate\Auth\Access\Response;

class QuestionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Question $question): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Question $question): bool
    {
        return ($question->user_id === $user->id) && !($question->resultItems()->exists());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Question $question): bool
    {
        return ($question->user_id === $user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Question $question): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Question $question): bool
    {
        return false;
    }

    public function addQuestionToBank(User $user, Question $question, Bank $bank):bool
    {
        return !$bank->questions()->where('id', $question->id)->exists() && $bank->user_id === $user->id && $bank->default==false;
    }

    public function removeQuestionFromBank(User $user, Question $question, Bank $bank):bool
    {
        return $bank->questions()->where('id', $question->id)->exists() && $bank->user_id === $user->id && $bank->default==false;
    }
}
