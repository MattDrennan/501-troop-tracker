<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Represents the possible outcomes of a user authentication attempt.
 *
 * This enum is used to provide a clear, type-safe status for login results,
 * making it easy to handle different scenarios like success, failure, or
 * if the user is banned.
 */
enum AuthenticationStatus: string
{
    use HasEnumHelpers;

    /**
     * The user provided valid credentials and is allowed to log in.
     */
    case SUCCESS = 'success';
    /**
     * The user provided invalid credentials (e.g., wrong password).
     */
    case FAILURE = 'failure';
    /**
     * The user's account is banned and they are not allowed to log in.
     */
    case BANNED = 'banned';
}