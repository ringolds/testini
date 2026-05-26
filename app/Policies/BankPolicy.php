<?php

namespace App\Policies;

use App\Models\Bank;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BankPolicy
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
    public function view(User $user, Bank $bank): bool
    {
        return ((($user->id === $bank->user_id) || ($bank->public==TRUE)) && $bank->deleted_at==NULL) || ($user->is_admin) ;
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
    public function update(User $user, Bank $bank): bool
    {
        return (($user->id === $bank->user_id && $bank->default == FALSE) || $user->is_admin) && $bank->deleted_at==NULL;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bank $bank): bool
    {
        return ($user->id === $bank->user_id || $user->is_admin) && $bank->default == FALSE && $bank->deleted_at==NULL;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bank $bank): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bank $bank): bool
    {
        return false;
    }
}
