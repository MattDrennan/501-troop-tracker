<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Defines the membership status of a trooper within an organization.
 */
enum MembershipStatus: string
{
    use HasEnumHelpers;

    /**
     * Not a member of the organization.
     */
    case None = 'none';

    /**
     * Membership application is pending approval.
     */
    case Pending = 'pending';

    /**
     * Denied member of the organization.
     */
    case Denied = 'denied';

    /**
     * Active member of the organization.
     */
    case Active = 'active';

    /**
     * A member on reserve status.
     */
    case Reserve = 'reserve';

    /**
     * A retired member.
     */
    case Retired = 'retired';
}