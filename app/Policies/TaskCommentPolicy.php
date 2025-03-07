<?php

namespace App\Policies;

use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskCommentPolicy
{
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
    public function view(User $user, TaskComment $taskComment): bool
    {
        $isCreator = $taskComment->user_id === $user->id;
        $isMember  = $taskComment->task->column->board->project->members()->where('id', $user->id)->exists();

        return $isCreator || $isMember;
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
    public function update(User $user, TaskComment $taskComment): bool
    {
        return $taskComment->user_id === $user->id;

    }

    /**
     * Determine whether the user can delete the model.
     */
        public function delete(User $user, TaskComment $taskComment): bool
        {
            $isCreator = $taskComment->user_id === $user->id;
            $isAdmin  = $taskComment->task->column->board->project->members()
                ->wherePivot('role', 'admin')
                ->where('id', $user->id)->exists();
        $isProjectOwner = $taskComment->task->column->board->project()->where('owner_id', $user->id)->exists();


            return $isCreator || $isAdmin || $isProjectOwner;
        }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskComment $taskComment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskComment $taskComment): bool
    {
        return false;
    }
}
