<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\Trooper;

class SettingPolicy
{
    use HasTrooperPermissionsTrait;

    /**
     * Determine whether the user can view any models.
     *
     * @param Trooper $trooper The user performing the action.
     * @return bool True if the user is an admin, false otherwise.
     */
    public function viewAny(Trooper $trooper): bool
    {
        return $this->isAdmin($trooper);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param Trooper $trooper The user performing the action.
     * @param Setting $setting The setting being viewed.
     * @return bool True if the user is an admin, false otherwise.
     */
    public function view(Trooper $trooper, Setting $setting): bool
    {
        return $this->isAdmin($trooper);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param Trooper $trooper The user performing the action.
     * @return bool True if the user is an admin, false otherwise.
     */
    public function create(Trooper $trooper): bool
    {
        return $this->isAdmin($trooper);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param Trooper $trooper The user performing the action.
     * @param Setting $setting The setting being updated.
     * @return bool True if the user is an admin, false otherwise.
     */
    public function update(Trooper $trooper, Setting $setting): bool
    {
        return $this->isAdmin($trooper);
    }

    /**
     * Determine whether the user can delete the model.
     * Deleting settings is not permitted.
     *
     * @param Trooper $trooper The user performing the action.
     * @param Setting $setting The setting being deleted.
     * @return bool Always returns false.
     */
    public function delete(Trooper $trooper, Setting $setting): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Trooper $trooper, Setting $setting): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Restoring settings is not permitted.
     *
     * @param Trooper $trooper The user performing the action.
     * @param Setting $setting The setting being restored.
     * @return bool Always returns false.
     */
    public function forceDelete(Trooper $trooper, Setting $setting): bool
    {
        return false;
    }
}
