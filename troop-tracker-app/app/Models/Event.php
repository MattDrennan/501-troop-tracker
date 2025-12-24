<?php

namespace App\Models;

use App\Models\Base\Event as BaseEvent;
use App\Models\Concerns\HasTrooperStamps;
use App\Models\Scopes\HasEventScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends BaseEvent
{
    use HasFactory;
    use HasEventScopes;
    use HasTrooperStamps;

    /**
     * Get the troopers signed up for the event.
     */
    public function event_troopers(): HasMany
    {
        return $this->hasMany(EventTrooper::class);
    }
}
