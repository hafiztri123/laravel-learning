<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
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
    public function view(User $user, Task $task): bool
    {
        $isCreator = $task->created_by === $user->id;
        $isMember = $task->column->board->project->members->contains($user);

        return $isCreator || $isMember;

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
    public function update(User $user, Task $task): bool
    {
        $isCreator = $task->created_by === $user->id;
        $isAdmin = $task->column->board->project->members()->where('role', 'admin')->exists();
        $isOwner = $task->column->board->project->id === $user->id;

        return $isCreator || $isAdmin || $isOwner;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        $isCreator = $task->created_by === $user->id;
        $isAdmin = $task->column->board->project->members()->where('role', 'admin')->exists();
        $isOwner = $task->column->board->project->id === $user->id;

        return $isCreator || $isAdmin || $isOwner;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
