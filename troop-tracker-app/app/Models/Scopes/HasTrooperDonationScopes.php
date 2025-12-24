<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait containing local scopes for the TrooperDonation model.
 */
trait HasTrooperDonationScopes
{
    /**
     * Scope a query to only include donations by a specific trooper.
     *
     * @param Builder<self> $query The Eloquent query builder.
     * @param int $trooper_id The ID of the trooper to filter by.
     * @return Builder<self>
     */
    protected function scopeByTrooper(Builder $query, int $trooper_id): Builder
    {
        return $query->where(self::TROOPER_ID, $trooper_id)
            ->orderBy(self::CREATED_AT, "desc");
    }

    /**
     * Scope a query to only include donations for a given month.
     *
     * @param Builder<self> $query The Eloquent query builder.
     * @param Carbon|null $date The date to determine the month. Defaults to the current month.
     * @return Builder<self>
     */
    protected function scopeForMonth(Builder $query, Carbon $date = null): Builder
    {
        $start_date = ($date ?? Carbon::now())->startOfMonth();

        $end_date = $start_date->endOfMonth();

        return $query->whereBetween(self::CREATED_AT, [$start_date, $end_date]);
    }
}