<?php

declare(strict_types=1);

namespace App\Models\Observers;

use App\Models\Trooper;
use App\Models\TrooperAchievement;

/**
 * Handles lifecycle events for the Trooper model.
 */
class TrooperObserver
{
    /**
     * Handle the Trooper "created" event.
     *
     * @param Trooper $trooper The trooper instance that was created.
     */
    public function created(Trooper $trooper): void
    {
        TrooperAchievement::create([
            'trooper_id' => $trooper->id,
        ]);
    }
}
