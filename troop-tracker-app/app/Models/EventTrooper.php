<?php

namespace App\Models;

use App\Enums\TrooperEventStatus;
use App\Models\Base\EventTrooper as BaseEventTrooper;
use App\Models\Concerns\HasTrooperStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventTrooper extends BaseEventTrooper
{
    use HasFactory;
    use HasTrooperStamps;

    protected function casts(): array
    {
        return array_merge($this->casts, [
            self::STATUS => TrooperEventStatus::class,
        ]);
    }
}
