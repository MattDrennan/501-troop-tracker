<?php

declare(strict_types=1);

namespace Tests\UnitTests\Actions;

use App\Actions\LoginAction;
use App\Domain\Services\AuthenticationService;
use App\Domain\Responses\LoginResponse;
use App\Requests\LoginRequest;
use App\Responders\LoginResponder;
use App\Responders\RedirectResponder;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class LoginActionTest extends TestCase
{
    private MockObject|AuthenticationService $authentication_service;
    private MockObject|LoginResponder $login_responder;
    private MockObject|RedirectResponder $redirect_responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authentication_service = $this->createMock(AuthenticationService::class);
        $this->login_responder = $this->createMock(LoginResponder::class);
        $this->redirect_responder = $this->createMock(RedirectResponder::class);
    }

    public function testItRedirectsOnSuccessfulPostRequest(): void
    {
        //  arrange
        $login_request = $this->createMock(LoginRequest::class);
        $login_request->method('isPost')->willReturn(true);

        $login_action = $this->createLoginAction($login_request);

        $this->authentication_service->expects($this->once())
            ->method('login')
            ->with($login_request)
            ->willReturn(new LoginResponse(true));

        $this->redirect_responder->expects($this->once())
            ->method('redirect')
            ->with('index.php');

        $this->login_responder->expects($this->once())->method('send');

        //  act
        $login_action->execute();
    }

    public function testItShowsFormAgainOnFailedPostRequest(): void
    {
        //  arrange
        $login_request = $this->createMock(LoginRequest::class);
        $login_request->method('isPost')->willReturn(true);
        $login_response = new LoginResponse(false, 'Bad credentials');

        $login_action = $this->createLoginAction($login_request);

        $this->authentication_service->expects($this->once())
            ->method('login')
            ->with($login_request)
            ->willReturn($login_response);

        $this->redirect_responder->expects($this->never())->method('redirect');

        $this->login_responder->expects($this->once())
            ->method('send')
            ->with($login_response);

        //  act 
        $login_action->execute();
    }

    public function testItShowsFormOnGetRequest(): void
    {
        //  arrange
        $login_request = $this->createMock(LoginRequest::class);
        $login_request->method('isPost')->willReturn(false);

        $login_action = $this->createLoginAction($login_request);

        $this->login_responder->expects($this->once())
            ->method('send');

        //  act
        $login_action->execute();
    }

    private function createLoginAction(LoginRequest $login_request): LoginAction
    {
        return new LoginAction($login_request, $this->login_responder, $this->authentication_service);
    }
}