<?php

namespace App\Policies;

use App\Models\NoteTag;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NoteTagPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, NoteTag $noteTag): bool
    {
        return $user->id === $noteTag->note->user_id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, NoteTag $noteTag): bool
    {
        return $user->id === $noteTag->note->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, NoteTag $noteTag): bool
    {
        return $user->id === $noteTag->note->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NoteTag $noteTag): bool
    {
        return $user->id === $noteTag->note->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NoteTag $noteTag): bool
    {
        return $user->id === $noteTag->note->user_id;
    }

}
