<?php

declare(strict_types=1);

namespace App\Utilities;

use App\Payloads\HttpRequest;

final class Router
{
    private array $routes = [];

    public function __construct(
        private readonly HttpRequest $request
    ) {
    }

    public function addRoute(string $route, string $action): void
    {
        $route = $this->cleanRoute($route);

        $this->routes[$route] = $action;
    }

    private function cleanRoute(string $route): string
    {
        return strtolower(ltrim($route, '/'));
    }

    public function execute(Container $container): void
    {
        $route = $this->request->get->get('a');

        $route = $this->cleanRoute($route);

        $action_class = $this->routes[$route];

        $action = $container->get($action_class);

        $result = $action->execute();

        $result->send();
    }
}