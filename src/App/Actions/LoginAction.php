<?php

declare(strict_types=1);

namespace App\Actions;

use App\Domain\Services\AuthenticationService;
use App\Requests\LoginRequest;
use App\Responders\HtmlResponder;
use App\Domain\Responses\LoginResponse;
use App\Results\ActionResult;

class LoginAction implements ActionInterface
{
    public function __construct(
        private readonly LoginRequest $request,
        private readonly HtmlResponder $responder,
        private readonly AuthenticationService $authenticationService,
    ) {
    }


    public function execute(): ActionResult
    {
        $response = new LoginResponse(false);

        // Handle the GET request case first
        if ($this->request->isPost()) {
            // Handle the POST request case
            $response = $this->authenticationService->login($this->request);
            if ($response->isSuccess()) {
                return $this->responder->redirect('index.php');
            }
        }
        return $this->responder->render('pages/login.html', $response->getData());
    }
}
