<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\EventUploadTag;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait containing local scopes for the EventUpload model.
 */
trait HasEventUploadScopes
{
    /**
     * Scope a query to only include uploads for a specific event.
     *
     * @param Builder<self> $query The Eloquent query builder.
     * @param int $event_id The ID of the event to filter by.
     * @return Builder<self>
     */
    protected function scopeByEvent(Builder $query, int $event_id): Builder
    {
        return $query->with('event_upload_tags')
            ->where(self::EVENT_ID, $event_id);
    }

    /**
     * Scope a query to only include uploads tagged with a specific trooper.
     *
     * @param Builder<self> $query The Eloquent query builder.
     * @param int $trooper_id The ID of the trooper to filter by.
     * @return Builder<self>
     */
    protected function scopeByTrooper(Builder $query, int $trooper_id): Builder
    {
        return $query->with('event_upload_tags')
            ->whereHas('event_upload_tags', fn($q) => $q->where(EventUploadTag::TROOPER_ID, $trooper_id));
    }
}