<?php

namespace App\Models;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Models\Base\Trooper as BaseTrooper;
use App\Models\Casts\LowerCast;
use App\Models\Concerns\HasObserver;
use App\Models\Scopes\HasTrooperScopes;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

/**
 * Represents a user of the application, typically a member of a costuming organization.
 * This model handles authentication, authorization, and user-specific data and relationships.
 */
class Trooper extends BaseTrooper implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;

    use HasFactory;
    use Notifiable;
    use HasTrooperScopes;
    use HasObserver;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts()
    {
        return array_merge($this->casts, [
            self::MEMBERSHIP_STATUS => MembershipStatus::class,
            self::MEMBERSHIP_ROLE => MembershipRole::class,
            self::EMAIL => LowerCast::class
        ]);
    }

    /**
     * Check if the trooper's membership role is admin.
     *
     * @return bool True if the trooper is admin, false otherwise.
     */
    public function isAdministrator(): bool
    {
        return $this->isActive() && $this->membership_role == MembershipRole::Administrator;
    }

    /**
     * Check if the trooper's membership role is moderator.
     *
     * @return bool True if the trooper is moderator, false otherwise.
     */
    public function isModerator(): bool
    {
        return $this->isActive() && $this->membership_role == MembershipRole::Moderator;
    }

    /**
     * Check if the trooper's membership status is active.
     *
     * @return bool True if the trooper is active, false otherwise.
     */
    public function isActive(): bool
    {
        return $this->membership_status == MembershipStatus::Active;
    }

    /**
     * Check if the trooper's membership status is denied.
     *
     * @return bool True if the trooper is denied, false otherwise.
     */
    public function isDenied(): bool
    {
        return $this->membership_status == MembershipStatus::Denied;
    }

    /**
     * Attach a costume to the trooper if it's not already attached.
     *
     * @param int $costume_id The ID of the costume to attach.
     */
    public function attachCostume(int $costume_id): void
    {
        if (!$this->costumes()->where(TrooperCostume::COSTUME_ID, $costume_id)->exists())
        {
            $this->costumes()->attach($costume_id);
        }
    }

    /**
     * Detach a costume from the trooper.
     *
     * @param int $costume_id The ID of the costume to detach.
     */
    public function detachCostume(int $costume_id): void
    {
        $this->costumes()->detach($costume_id);
    }

    /**
     * Check if the trooper has an active status in any of their assigned organizations.
     *
     * @return bool True if at least one active assignment exists, false otherwise.
     */
    public function hasActiveOrganizationStatus(): bool
    {
        $has_assignment = $this->trooper_assignments()
            ->where(TrooperAssignment::MEMBER, true)
            ->exists();

        return $has_assignment;
    }

    /**
     * Get the trooper's active assignments, optionally filtered by a parent organization.
     *
     * @param int|null $organization_id The ID of the parent organization to filter by.
     *
     * @return Collection<int, Organization> A collection of active organizations.
     */
    public function getAssignedOrganizations(?int $organization_id): Collection
    {
        $query = $this->trooper_assignments()
            ->where(TrooperAssignment::MEMBERSHIP_STATUS, MembershipStatus::Active);

        if ($organization_id)
        {
            $query->where(TrooperAssignment::ORGANIZATION_ID, $organization_id);
        }

        return $query->with('organization')->get()->map->organization;
    }
}