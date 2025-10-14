<?php

declare(strict_types=1);

namespace App\Requests;


/**
 * Represents and validates the data for a login request.
 */
class LoginRequest
{
    public function __construct(private readonly HttpRequest $request)
    {
    }

    public function getTkid(): string
    {
        return (string) $this->request->post->get('tkid', '');
    }

    public function getPassword(): string
    {
        return (string) $this->request->post->get('password', '');
    }

    public function stayLoggedIn(): bool
    {
        return $this->request->post->get('keepLog') !== null;
    }

    public function isGet(): bool
    {
        return $this->request->isGet();
    }

    public function isPost(): bool
    {
        return $this->request->isPost();
    }
}