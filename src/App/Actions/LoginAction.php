<?php

declare(strict_types=1);

namespace App\Actions;

use App\Domain\Results\LoginResult;
use App\Domain\Services\AuthenticationService;
use App\Payloads\LoginPayload;
use App\Responders\HtmlResponder;
use App\Responders\RedirectResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginAction
{
    public function __construct(
        private readonly HtmlResponder $html_responder,
        private readonly RedirectResponder $redirect_responder,
        private readonly AuthenticationService $service,
    ) {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $result = new LoginResult(false);
        $payload = new LoginPayload($request->getParsedBody() ?? []);

        if ($request->getMethod() == 'POST' && $payload->isValid()) {
            $result = $this->service->login($payload);
            if ($result->isSuccess()) {
                return $this->redirect_responder->respond($response, '/index.php');
            }
        }

        $data = $result->getDataPayload('password');

        return $this->html_responder->respond($response, 'pages/login.html', $data);
    }
}
