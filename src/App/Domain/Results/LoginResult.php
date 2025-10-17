<?php

declare(strict_types=1);

namespace App\Domain\Results;

use App\Utilities\PayloadableTrait;

/**
 * Represents the result of a login attempt from the AuthenticationService.
 */
class LoginResult
{
    use PayloadableTrait;

    /**
     * LoginResult constructor.
     *
     * @param bool $is_success Whether the login attempt was successful.
     * @param string|null $error_message An error message if the login failed.
     */
    public function __construct(
        protected readonly bool $is_success,
        protected readonly ?string $error_message = null
    ) {
    }

    /**
     * Checks if the login attempt was successful.
     */
    public function isSuccess(): bool
    {
        return $this->is_success;
    }

    /**
     * Gets the error message if the login attempt failed.
     */
    public function getErrorMessage(): ?string
    {
        return $this->error_message;
    }
}