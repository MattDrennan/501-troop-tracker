<?php

namespace App\Policies;

use App\Models\Notice;
use App\Models\Trooper;

/**
 * Class NoticePolicy
 *
 * Defines authorization rules for organization-related actions.
 */
class NoticePolicy
{
    use HasTrooperPermissionsTrait;

    /**
     * Determine whether the user can create organizations.
     *
     * @param Trooper $trooper The authenticated user performing the action.
     * @return bool True if the user is an administrator, false otherwise.
     */
    public function create(Trooper $trooper): bool
    {
        return $this->isAdmin($trooper) || $this->isModerator($trooper);
    }

    /**
     * Determine whether the user can update an organization.
     *
     * @param Trooper $trooper The authenticated user performing the action.
     * @param Notice $subject The organization being updated.
     * @return bool True if the user can moderate the subject organization, false otherwise.
     */
    public function update(Trooper $trooper, Notice $subject): bool
    {
        return $this->canModerate($trooper, $subject);
    }

    /**
     * Determine whether the user can delete an organization.
     * Deleting organizations is not permitted through this policy.
     *
     * @param Trooper $trooper The authenticated user performing the action.
     * @param Notice $subject The organization being deleted.
     * @return bool Always false.
     */
    public function delete(Trooper $trooper, Notice $subject): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore an organization.
     * Restoring organizations is not permitted through this policy.
     *
     * @param Trooper $trooper The authenticated user performing the action.
     * @param Notice $subject The organization being restored.
     * @return bool Always false.
     */
    public function restore(Trooper $trooper, Notice $subject): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete an organization.
     * Force deleting organizations is not permitted through this policy.
     *
     * @param Trooper $trooper The authenticated user performing the action.
     * @param Notice $subject The organization being force-deleted.
     * @return bool Always false.
     */
    public function forceDelete(Trooper $trooper, Notice $subject): bool
    {
        return false;
    }

    /**
     * Check if a user can moderate a subject organization.
     * An admin can moderate any organization. A moderator can moderate organizations within their assigned scope.
     *
     * @param Trooper $trooper The user performing the action (moderator).
     * @param Notice $subject The organization being moderated.
     * @return bool True if the user has moderation rights over the subject organization, false otherwise.
     */
    private function canModerate(Trooper $trooper, Notice $subject): bool
    {
        if ($this->isAdmin($trooper))
        {
            return true;
        }

        return Notice::moderatedBy($trooper)
            ->where(Notice::ID, $subject->id)
            ->exists();
    }
}
