<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AuthenticationInterface;
use App\Contracts\ForumInterface;
use App\Enums\AuthenticationStatus;
use App\Models\Trooper;
use Exception;
use Illuminate\Support\Facades\Hash;

/**
 * Provides authentication and forum-related services for a standalone application instance
 * without an external forum integration like Xenforo.
 */
class StandaloneService implements AuthenticationInterface, ForumInterface
{
    /**
     * Retrieves the avatar URL for a user. Not implemented in this service.
     *
     * @param mixed $forum_user_id The user's forum ID.
     * @return string An empty string or throws an exception.
     * @throws Exception As this feature is not implemented.
     */
    public function getAvatarUrl(mixed $forum_user_id): string
    {
        throw new Exception("not implemented");
    }

    /**
     * Authenticates a user against the local database.
     *
     * @param string $username The user's username.
     * @param string $password The user's password.
     * @return AuthenticationStatus The result of the authentication attempt.
     */
    public function authenticate(string $username, string $password): AuthenticationStatus
    {
        $trooper = Trooper::where(Trooper::USERNAME, $username)->first();

        if ($trooper && Hash::check($password, $trooper->password))
        {
            return AuthenticationStatus::SUCCESS;
        }

        return AuthenticationStatus::FAILURE;
    }

    /**
     * Verifies a user's credentials against the local database.
     *
     * @param string $username The user's username.
     * @param string $password The user's password.
     * @return mixed The trooper model if verification is successful, otherwise null.
     */
    public function verify(string $username, string $password): ?Trooper
    {
        $trooper = Trooper::where(Trooper::USERNAME, $username)->first();

        if ($trooper && Hash::check($password, $trooper->password))
        {
            return $trooper;
        }

        return null;
    }
}