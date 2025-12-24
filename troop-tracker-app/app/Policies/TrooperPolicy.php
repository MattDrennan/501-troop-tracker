<?php

namespace App\Policies;

use App\Enums\MembershipRole;
use App\Models\Trooper;

/**
 * Class TrooperPolicy
 *
 * Defines authorization rules for trooper-related actions.
 */
class TrooperPolicy
{
    use HasTrooperPermissionsTrait;

    /**
     * Determine whether the user can view a specific trooper.
     *
     * @param Trooper $trooper The authenticated user performing the action.
     * @param Trooper $subject The trooper being viewed.
     * @return bool True if the user can moderate the subject, false otherwise.
     */
    public function view(Trooper $trooper, Trooper $subject): bool
    {
        return $this->canModerate($trooper, $subject);
    }

    /**
     * Determine whether the user can create troopers.
     * Always returns false as creation is handled by the registration process.
     *
     * @param Trooper $trooper The authenticated user performing the action.
     * @return bool Always false.
     */
    public function create(Trooper $trooper): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update a trooper.
     *
     * @param Trooper $trooper The authenticated user performing the action.
     * @param Trooper $subject The trooper being updated.
     * @return bool True if the user can moderate the subject, false otherwise.
     */
    public function update(Trooper $trooper, Trooper $subject): bool
    {
        return $this->canModerate($trooper, $subject);
    }

    /**
     * Determine whether the user can delete a trooper.
     * Deleting troopers is not permitted through this policy.
     *
     * @param Trooper $trooper The authenticated user performing the action.
     * @param Trooper $subject The trooper being deleted.
     * @return bool Always false.
     */
    public function delete(Trooper $trooper, Trooper $subject): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore a trooper.
     * Restoring troopers is not permitted through this policy.
     *
     * @param Trooper $trooper The authenticated user performing the action.
     * @param Trooper $subject The trooper being restored.
     * @return bool Always false.
     */
    public function restore(Trooper $trooper, Trooper $subject): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete a trooper.
     * Force deleting troopers is not permitted through this policy.
     *
     * @param Trooper $trooper The authenticated user performing the action.
     * @param Trooper $subject The trooper being force-deleted.
     * @return bool Always false.
     */
    public function forceDelete(Trooper $trooper, Trooper $subject): bool
    {
        return false;
    }

    /**
     * Determine whether the user can approve a trooper.
     *
     * @param Trooper $trooper The authenticated user performing the action.
     * @param Trooper $subject The trooper being approved.
     * @return bool True if the user can moderate the subject, false otherwise.
     */
    public function approve(Trooper $trooper, Trooper $subject): bool
    {
        return $this->canModerate($trooper, $subject);
    }

    /**
     * Check if a user can moderate a subject trooper.
     * An admin can moderate any trooper. A moderator can moderate troopers within their assigned scope.
     *
     * @param Trooper $trooper The user performing the action (moderator).
     * @param Trooper $subject The trooper being moderated.
     * @return bool True if the user has moderation rights over the subject, false otherwise.
     */
    private function canModerate(Trooper $trooper, Trooper $subject): bool
    {
        if ($this->isAdmin($trooper))
        {
            return true;
        }

        return Trooper::moderatedBy($trooper)
            ->where(Trooper::ID, $subject->id)
            ->exists();
    }
}
