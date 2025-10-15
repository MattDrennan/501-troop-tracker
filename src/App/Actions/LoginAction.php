<?php

declare(strict_types=1);

namespace App\Actions;

use App\Domain\Results\LoginResult;
use App\Domain\Services\AuthenticationService;
use App\Payloads\LoginPayload;
use App\Responders\LoginResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginAction
{
    public function __construct(
        private readonly LoginResponder $responder,
        private readonly AuthenticationService $service,
    ) {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $result = new LoginResult(false);
        $payload = new LoginPayload($request->getParsedBody() ?? []);

        if ($request->getMethod() == 'POST' && $payload->isValid()) {
            $result = $this->service->login($payload);
        }

        return $this->responder->respond($request, $response, $result);
    }
}
