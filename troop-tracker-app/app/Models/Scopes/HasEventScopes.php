<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\EventTrooper;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait containing local scopes for the Event model.
 */
trait HasEventScopes
{
    /**
     * Scope a query to find events a specific trooper is signed up for.
     *
     * This scope filters events based on a trooper's participation and whether the
     * event is open or closed. It also eagerly loads the relevant relationships
     * for displaying the event details for that trooper.
     *
     * @param Builder<self> $query The Eloquent query builder.
     * @param int $trooper_id The ID of the trooper to filter by.
     * @param bool $closed True to fetch closed (historical) events, false for open events.
     * @return Builder<self>
     */
    protected function scopeByTrooper(Builder $query, int $trooper_id, bool $closed): Builder
    {
        $with = [
            'event_troopers' => function ($q) use ($trooper_id)
            {
                $q->where(EventTrooper::TROOPER_ID, $trooper_id)
                    ->with('costume.organization');
            },
        ];

        return $query->with($with)
            ->where(self::CLOSED, $closed)
            ->whereHas('event_troopers', function ($q) use ($trooper_id)
            {
                $q->where(EventTrooper::TROOPER_ID, $trooper_id);
            });
    }
}