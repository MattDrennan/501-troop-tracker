<?php

declare(strict_types=1);

namespace App\Actions;

use App\Domain\Services\AuthenticationService;
use App\Requests\LoginRequest;
use App\Responders\LoginResponder;
use App\Domain\Responses\LoginResponse;

class LoginAction
{
    public function __construct(
        private readonly LoginRequest $request,
        private readonly LoginResponder $responder,
        private readonly AuthenticationService $authenticationService,
    ) {
    }


    public function execute(): void
    {
        // Handle the GET request case first
        if (!$this->request->isPost()) {
            $this->responder->send(new LoginResponse(false));
            return;
        }

        // Handle the POST request case
        $response = $this->authenticationService->login($this->request);
        if ($response->isSuccess()) {
            $this->responder->redirect('index.php');
            return;
        }
        $this->responder->send($response);
    }
}

