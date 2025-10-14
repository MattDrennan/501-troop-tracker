<?php

declare(strict_types=1);

namespace App\Domain\Responses;

/**
 * Represents the result of a login attempt from the AuthenticationService.
 */
class LoginResponse
{
    public function __construct(
        private readonly bool $isSuccess,
        private readonly ?string $errorMessage = null
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}