<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PhotoOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class PhotoOrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_photo::order');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PhotoOrder $photoOrder): bool
    {
        return $user->can('view_photo::order');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_photo::order');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PhotoOrder $photoOrder): bool
    {
        return $user->can('update_photo::order');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PhotoOrder $photoOrder): bool
    {
        return $user->can('delete_photo::order');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_photo::order');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, PhotoOrder $photoOrder): bool
    {
        return $user->can('force_delete_photo::order');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_photo::order');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, PhotoOrder $photoOrder): bool
    {
        return $user->can('restore_photo::order');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_photo::order');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, PhotoOrder $photoOrder): bool
    {
        return $user->can('replicate_photo::order');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_photo::order');
    }
}
