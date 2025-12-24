<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\TrooperAward;
use Illuminate\Database\Eloquent\Builder;

trait HasTrooperAwardScopes
{
    protected function scopeByTrooper(Builder $query, int $trooper_id): Builder
    {
        return $query->with('award')
            ->where(self::TROOPER_ID, $trooper_id)
            ->orderBy(self::CREATED_AT, "desc");
    }
}