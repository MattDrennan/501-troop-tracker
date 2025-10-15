<?php

namespace App\Responders;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

trait RequestableTrait
{
    /**
     * Checks if the current request method is POST.
     *
     * @return bool True if the request method is POST, false otherwise.
     */
    public function isPost(Request $request): bool
    {
        return $request->getMethod() === 'POST';
    }

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