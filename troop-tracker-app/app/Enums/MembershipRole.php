<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Defines the membership status of a trooper within an organization.
 */
enum MembershipRole: string
{
    use HasEnumHelpers;

    /**
     * An regular member.
     */
    case Member = 'member';

    /**
     * A non-costumed handler.
     */
    case Handler = 'handler';

    /**
     * A member with moderation privileges.
     */
    case Moderator = 'moderator';

    /**
     * An regular member.
     */
    case Administrator = 'administrator';
}