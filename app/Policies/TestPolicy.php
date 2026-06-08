<?php

namespace App\Policies;

use App\Models\Test;
use App\Models\User;
use App\Models\Bank;
use Illuminate\Auth\Access\Response;

class TestPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        $exceptions = ['start', 'continue'];

        if ($user->is_admin) {
            if (in_array($ability, $exceptions)) {
                return null; 
            }

            return true;
        }

        return null; 
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Test $test): bool
    {
        return $test->user_id === $user->id || $test->public === true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Test|int $test): bool
    {
        if($test instanceof Test){
            return $test->user_id === $user->id;
        }
        else{
            return Test::where('id', $test)->where('user_id', $user->id)->exists();
        }
        
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Test $test): bool
    {
        return $test->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Test $test): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Test $test): bool
    {
        return false;
    }

    public function addExistingQuestion(User $user, Test $test): bool
    {
        return ($user->id === $test->user_id);
    }

    public function addBankToTest(User $user, Test $test, Bank $bank): bool
    {
        return $user->id === $test->user_id && !$test->banks()->where('id', $bank->id)->exists();
    }

    public function removeBankFromTest(User $user, Test $test, Bank $bank): bool
    {
        return $user->id === $test->user_id && $test->banks()->where('id', $bank->id)->exists();
    }

    public function changeBankCount(User $user, Test $test, Bank $bank): bool
    {
        return $user->id === $test->user_id && $test->banks()->where('id', $bank->id)->exists();
    }

    public function start(User $user, Test $test): bool
    {
        return !$user->results()->where('test_id', $test->id)->whereNull('end_time')->exists() 
        && ($test->user_id==$user->id || $test->public==true);
    }

    public function continue(User $user, Test $test): bool
    {
        return $user->results()->where('test_id', $test->id)->whereNull('end_time')->exists() 
        && ($test->user_id==$user->id || $test->public==true);
    }
}
