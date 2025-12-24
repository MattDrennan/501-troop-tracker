<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Enums\AuthenticationStatus;

interface AuthenticationInterface
{
    /**
     * Authenticates a user.
     *
     * @param string $username The user's forum username.
     * @param string $password The user's password.
     * @return AuthenticationStatus The result of the authentication attempt.
     */
    public function authenticate(string $username, string $password): AuthenticationStatus;

    /**
     * Verifies a user.
     *
     * @param string $username The user's forum username.
     * @param string $password The user's password.
     * @return mixed NULL if not verified else user->id.
     */
    public function verify(string $username, string $password): mixed;
}