<?php

declare(strict_types=1);

namespace App\Requests;

/**
 * A container for key-value pairs (e.g., POST, GET, session data).
 */
class ParameterBag
{
    public function __construct(private readonly array $parameters = [])
    {
    }

    /**
     * Gets a parameter by its key.
     *
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->parameters[$key] ?? $default;
    }
}

/**
 * Represents an HTTP request, encapsulating superglobals.
 */
class HttpRequest
{
    public readonly ParameterBag $get;
    public readonly ParameterBag $post;
    public readonly ParameterBag $session;
    public readonly ParameterBag $server;

    public function __construct(array $get = [], array $post = [], array $session = [], array $server = [])
    {
        $this->get = new ParameterBag($get);
        $this->post = new ParameterBag($post);
        $this->session = new ParameterBag($session);
        $this->server = new ParameterBag($server);
    }

    /**
     * Creates a new Request object from PHP's superglobals.
     */
    public static function createFromGlobals(): self
    {
        return new self($_GET ?? [], $_POST ?? [], $_SESSION ?? [], $_SERVER ?? []);
    }

    public function isGet(): bool
    {
        return $this->server->get('REQUEST_METHOD') === 'GET';
    }

    public function isPost(): bool
    {
        return $this->server->get('REQUEST_METHOD') === 'POST';
    }
}