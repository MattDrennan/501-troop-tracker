<?php

declare(strict_types=1);

namespace App\Payloads;

use App\Utilities\PayloadableTrait;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;
use Respect\Validation\Validatable;


/**
 * Represents and validates the data for a login request.
 */
class LoginPayload
{
    use ValidatablePayloadTrait;
    use PayloadableTrait;

    protected ?string $tk_id;
    protected ?string $password;
    protected bool $stay_logged_in;

    public function __construct(array $data)
    {
        $this->tk_id = $data['tkid'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->stay_logged_in = $data['keepLog'] ?? false;
    }

    /**
     * Get the username (TKID) from the request.
     *
     * @return string|null The username, or null if not set.
     */
    public function getUsername(): string
    {
        return $this->tk_id ?? null;
    }

    /**
     * Get the password from the request.
     *
     * @return string|null The password, or null if not set.
     */
    public function getPassword(): string
    {
        return $this->password ?? null;
    }

    /**
     * Check if the 'stay logged in' option is set.
     *
     * @return bool True if 'stay logged in' is checked, false otherwise.
     */
    public function stayLoggedIn(): bool
    {
        return $this->stay_logged_in;
    }

    /**
     * Get the validation rules for the login request.
     *
     * @return Validatable The validator instance.
     */
    protected function getValidator(): Validatable
    {
        $v = v::key('tk_id', v::notEmpty())
            ->key('password', v::notEmpty());

        return $v;
    }
}