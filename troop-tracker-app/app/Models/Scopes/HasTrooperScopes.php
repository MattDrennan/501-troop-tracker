<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Models\Trooper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Trait containing local scopes for the Trooper model.
 */
trait HasTrooperScopes
{
    /**
     * Scope a query to find a trooper by their username.
     *
     * @param Builder<self> $query The Eloquent query builder.
     * @param string $username The username to search for.
     * @return Builder<self>
     */
    protected function scopeByUsername(Builder $query, string $username): Builder
    {
        return $query->where(self::USERNAME, $username);
    }

    /**
     * Scope a query to find all troopers not approved.
     *
     * @param Builder<self> $query The Eloquent query builder.
     * @return Builder<self>
     */
    protected function scopePendingApprovals(Builder $query): Builder
    {
        return $query
            ->where(self::MEMBERSHIP_STATUS, MembershipStatus::Pending)
            ->orderBy(self::NAME);
    }

    /**
     * Scope: limit to troopers that can be approved by a given moderator.
     *
     * @param Builder $query
     * @param Trooper $moderator
     * @return Builder
     */
    protected function scopeModeratedBy(Builder $query, Trooper $moderator): Builder
    {
        return $query->whereExists(function ($sub) use ($moderator)
        {
            $sub->select(DB::raw(1))
                ->from('tt_trooper_assignments as ta_moderator')
                ->join('tt_organizations as org_moderator', 'ta_moderator.organization_id', '=', 'org_moderator.id')
                ->join('tt_trooper_assignments as ta_candidate', 'ta_candidate.trooper_id', '=', 'tt_troopers.id')
                ->join('tt_organizations as org_candidate', 'ta_candidate.organization_id', '=', 'org_candidate.id')
                ->where('ta_moderator.trooper_id', $moderator->id)
                ->where('ta_moderator.moderator', true)
                ->whereRaw('org_candidate.node_path LIKE CONCAT(org_moderator.node_path, "%")');
        });
    }
}