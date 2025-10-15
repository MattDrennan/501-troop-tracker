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
    private MockObject|LoginResponder $responder;
    private MockObject|RedirectResponder $redirect_responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authentication_service = $this->createMock(AuthenticationService::class);
        $this->responder = $this->createMock(LoginResponder::class);
        $this->redirect_responder = $this->createMock(RedirectResponder::class);
    }

    public function testItRedirectsOnSuccessfulPostRequest(): void
    {
        //  arrange
        $request = $this->createMock(LoginRequest::class);
        $request->method('isPost')->willReturn(true);

        $action = $this->createLoginAction($request);

        $this->authentication_service->expects($this->once())
            ->method('login')
            ->with($request)
            ->willReturn(new LoginResponse(true));

        $this->redirect_responder->expects($this->once())
            ->method('redirect')
            ->with('index.php');

        $this->responder->expects($this->once())->method('send');

        //  act
        $action->execute();
    }

    public function testItShowsFormAgainOnFailedPostRequest(): void
    {
        //  arrange
        $request = $this->createMock(LoginRequest::class);
        $request->method('isPost')->willReturn(true);
        $response = new LoginResponse(false, 'Bad credentials');

        $action = $this->createLoginAction($request);

        $this->authentication_service->expects($this->once())
            ->method('login')
            ->with($request)
            ->willReturn($response);

        $this->redirect_responder->expects($this->never())->method('redirect');

        $this->responder->expects($this->once())
            ->method('send')
            ->with($response);

        //  act 
        $action->execute();
    }

    public function testItShowsFormOnGetRequest(): void
    {
        //  arrange
        $request = $this->createMock(LoginRequest::class);
        $request->method('isPost')->willReturn(false);

        $action = $this->createLoginAction($request);

        $this->responder->expects($this->once())
            ->method('send');

        //  act
        $action->execute();
    }

    private function createLoginAction(LoginRequest $request): LoginAction
    {
        return new LoginAction($request, $this->responder, $this->authentication_service);
    }
}