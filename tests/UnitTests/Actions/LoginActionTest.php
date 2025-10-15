<?php

declare(strict_types=1);

namespace Tests\UnitTests\Actions;

use App\Actions\LoginAction;
use App\Domain\Services\AuthenticationService;
use App\Domain\Responses\LoginResponse;
use App\Requests\LoginRequest;
use App\Responders\HtmlResponder;
use App\Responders\RedirectResponder;
use App\Results\HtmlResult;
use App\Results\RedirectResult;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class LoginActionTest extends TestCase
{
    private MockObject|AuthenticationService $authentication_service;
    private MockObject|HtmlResponder $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authentication_service = $this->createMock(AuthenticationService::class);
        $this->responder = $this->createMock(HtmlResponder::class);
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

        //  act
        $result = $action->execute();

        //  assert
        $this->assertInstanceOf(RedirectResult::class, $result);
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

        //  act 
        $result = $action->execute();

        //  assert
        $this->assertInstanceOf(HtmlResult::class, $result);
    }

    public function testItShowsFormOnGetRequest(): void
    {
        //  arrange
        $request = $this->createMock(LoginRequest::class);
        $request->method('isPost')->willReturn(false);

        $action = $this->createLoginAction($request);

        //  act
        $result = $action->execute();

        //  assert
        $this->assertInstanceOf(HtmlResult::class, $result);
    }

    private function createLoginAction(LoginRequest $request): LoginAction
    {
        return new LoginAction($request, $this->responder, $this->authentication_service);
    }
}