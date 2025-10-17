<?php

namespace App\Actions;

use Psr\Http\Message\RequestInterface as Request;

trait RequestableTrait
{
    /**
     * Checks if the client expects a JSON response based on the 'Accept' header.
     *
     * @return bool True if the client expects JSON, false otherwise.
     */
    public function expectsJson(Request $request): bool
    {
        $accept = $request->getHeaderLine('Accept') ?? '';

        return strtolower($accept) === 'application/json';
    }
}