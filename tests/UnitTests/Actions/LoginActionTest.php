<?php

declare(strict_types=1);

namespace Tests\UnitTests\Actions;

use App\Payloads\LoginPayload;
use App\Domain\Results\LoginResult;
use App\Responders\HtmlResponder;
use App\Responders\RedirectResponder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Actions\LoginAction;
use App\Domain\Services\AuthenticationService;

class LoginActionTest extends TestCase
{
    private MockObject|HtmlResponder $html_responder;
    private MockObject|RedirectResponder $redirect_responder;
    private MockObject|AuthenticationService $service;
    private LoginAction $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->redirect_responder = $this->createMock(RedirectResponder::class);
        $this->html_responder = $this->createMock(HtmlResponder::class);
        $this->service = $this->createMock(AuthenticationService::class);

        $this->subject = new LoginAction($this->html_responder, $this->redirect_responder, $this->service);
    }

    public function testInvokeHandlesGetRequest(): void
    {
        // Arrange
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $this->service->expects($this->never())->method('login');

        $callback = $this->callback(function ($arg) {
            return is_array($arg);
        });

        $this->html_responder->expects($this->once())
            ->method('respond')
            ->with($response, 'pages/login.html', $callback)
            ->willReturn($response);

        // Act
        $actual_response = ($this->subject)($request, $response);

        // Assert
        $this->assertSame($response, $actual_response);
    }

    public function testInvokeHandlesInvalidPostRequest(): void
    {
        // Arrange
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $request->expects($this->atLeastOnce())
            ->method('getMethod')
            ->willReturn('POST');

        $request->expects($this->atLeastOnce())
            ->method('getParsedBody')
            ->willReturn(['tkid' => 'TK-123']);

        $responder_callback = $this->callback(function ($arg) {
            return is_array($arg);
        });

        $this->html_responder->expects($this->once())
            ->method('respond')
            ->with($response, 'pages/login.html', $responder_callback)
            ->willReturn($response);

        // Act
        $actual_response = ($this->subject)($request, $response);

        // Assert
        $this->assertSame($response, $actual_response);
    }

    public function testInvokeHandlesInvalidCredentialPostRequest(): void
    {
        // Arrange
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $request->expects($this->atLeastOnce())
            ->method('getMethod')
            ->willReturn('POST');

        $request->expects($this->atLeastOnce())
            ->method('getParsedBody')
            ->willReturn(['tkid' => 'TK-123', 'password' => 'dontlook']);

        $service_callback = $this->callback(function ($arg) {
            return $arg instanceof LoginPayload;
        });

        $this->service->expects($this->once())
            ->method('login')
            ->with($service_callback)
            ->willReturn(new LoginResult(false));

        $responder_callback = $this->callback(function ($arg) {
            return is_array($arg);
        });

        $this->html_responder->expects($this->once())
            ->method('respond')
            ->with($response, 'pages/login.html', $responder_callback)
            ->willReturn($response);

        // Act
        $actual_response = ($this->subject)($request, $response);

        // Assert
        $this->assertSame($response, $actual_response);
    }

    public function testInvokeHandlesValidPostRequest(): void
    {
        // Arrange
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $request->expects($this->atLeastOnce())
            ->method('getMethod')
            ->willReturn('POST');

        $request->expects($this->atLeastOnce())
            ->method('getParsedBody')
            ->willReturn(['tkid' => 'TK-123', 'password' => 'dontlook']);

        $service_callback = $this->callback(function ($arg) {
            return $arg instanceof LoginPayload;
        });

        $this->service->expects($this->once())
            ->method('login')
            ->with($service_callback)
            ->willReturn(new LoginResult(true));

        $this->redirect_responder->expects($this->once())
            ->method('respond')
            ->with($response, '/index.php')
            ->willReturn($response);

        // Act
        $actual_response = ($this->subject)($request, $response);

        // Assert
        $this->assertSame($response, $actual_response);
    }
}
