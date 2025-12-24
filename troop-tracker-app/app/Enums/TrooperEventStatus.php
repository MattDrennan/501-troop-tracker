<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Defines the possible statuses for a trooper's attendance at an event.
 */
enum TrooperEventStatus: string
{
    use HasEnumHelpers;

    /**
     * No status set.
     */
    case None = 'none';
    /**
     * Trooper is confirmed to be going.
     */
    case Going = 'going';
    /**
     * Trooper is on standby for the event.
     */
    case StandBy = 'standby';
    /**
     * Trooper is tentatively planning to attend.
     */
    case Tentative = 'tentative';
    /**
     * Trooper has attended the event.
     */
    case Attended = 'attended';
    /**
     * Trooper has canceled their attendance.
     */
    case Canceled = 'canceled';
    /**
     * Trooper's attendance is pending approval.
     */
    case Pending = 'pending';
    /**
     * Trooper was not selected for a limited event.
     */
    case NotPicked = 'notpicked';
    /**
     * Trooper was confirmed but did not show up.
     */
    case NoShow = 'noshow';
}
