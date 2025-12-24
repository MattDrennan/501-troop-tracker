<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Trait containing local scopes for the Costume model.
 */
trait HasCostumeScopes
{
    /**
     * Scope a query to exclude a set of costumes by their IDs.
     *
     * @param Builder<self> $query The Eloquent query builder.
     * @param array<int> $costume_ids An array of costume IDs to exclude from the query results.
     * @return Builder<self>
     */
    protected function scopeExcluding(Builder $query, Collection|array $costume_ids): Builder
    {
        return $query->whereNotIn(self::ID, $costume_ids);
    }
}