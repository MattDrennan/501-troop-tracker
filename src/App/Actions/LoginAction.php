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
        private readonly LoginRequest $loginRequest,
        private readonly LoginResponder $loginResponder,
        private readonly AuthenticationService $authenticationService,
    ) {
    }


    public function execute(): void
    {
        // Handle the GET request case first
        if (!$this->loginRequest->isPost()) {
            $this->loginResponder->send(new LoginResponse(false));
            return;
        }

        // Handle the POST request case
        $response = $this->authenticationService->login($this->loginRequest);
        if ($response->isSuccess()) {
            $this->redirectResponder->redirect('index.php');
            return;
        }
        $this->loginResponder->send($response);
    }
}

