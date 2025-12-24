<?php

declare(strict_types=1);

namespace App\Models\Observers;

use App\Models\TrooperAssignment;
use Exception;

/**
 * Handles lifecycle events for the TrooperAssignment model.
 */
class TrooperAssignmentObserver
{
    /**
     * Handle the TrooperAssignment "saving" event.
     *
     * Enforces the business rule that a trooper can only be a "member" of an
     * organization that is a leaf node (has no children).
     *
     * @param TrooperAssignment $trooper_assignment The trooper assignment instance that was created.
     * @throws Exception if a trooper is assigned as a member to a non-leaf organization.
     */
    public function saving(TrooperAssignment $trooper_assignment): void
    {
        if ($trooper_assignment->member)
        {
            $organization = $trooper_assignment->organization;

            if ($organization->organizations()->count() > 0)
            {
                throw new Exception("Troopers can only be members at the lowest organizational level.");
            }
        }
    }
}
