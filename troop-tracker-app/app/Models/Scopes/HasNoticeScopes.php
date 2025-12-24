<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\Trooper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Trait containing local scopes for the Notice model.
 */
trait HasNoticeScopes
{
    /**
     * Scope: only notifications active at the current time.
     *
     * @param Builder<self> $query The Eloquent query builder.
     * @return Builder<self>
     */
    protected function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query->where(self::STARTS_AT, '<=', $now)
            ->where(function ($q) use ($now)
            {
                $q->whereNull(self::ENDS_AT)
                    ->orWhere(self::ENDS_AT, '>=', $now);
            });
    }

    /**
     * Scope a query to only include notifications visible to a given trooper.
     *
     * A notification is visible if the trooper is a member or moderator of an organization
     * that is an ancestor of (or the same as) the notification's organization.
     *
     * @param Builder<self> $query The Eloquent query builder.
     * @param Trooper $trooper The trooper whose visibility is being checked.
     * @return Builder<self>
     */
    protected function scopeVisibleTo(Builder $query, Trooper $trooper): Builder
    {
        return $query->whereExists(function ($sub) use ($trooper)
        {
            $sub->select(DB::raw(1))
                ->from('tt_trooper_assignments as ta_assign')
                ->join('tt_organizations as org_assign', 'ta_assign.organization_id', '=', 'org_assign.id')
                ->join('tt_organizations as org_notice', 'tt_notices.organization_id', '=', 'org_notice.id')
                ->where('ta_assign.trooper_id', $trooper->id)
                ->where('ta_assign.member', true)
                ->whereRaw('org_assign.node_path LIKE CONCAT(org_notice.node_path, "%")');
        });
    }


    /**
     * Scope: limit to organizations that can be updated by a given moderator.
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
                ->join('tt_organizations as org_notice', 'tt_notices.organization_id', '=', 'org_notice.id')
                ->where('ta_moderator.trooper_id', $moderator->id)
                ->where('ta_moderator.moderator', true)
                ->whereRaw('org_notice.node_path LIKE CONCAT(org_moderator.node_path, "%")');
        });
    }
}