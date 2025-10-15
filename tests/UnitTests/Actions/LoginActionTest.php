<?php

declare(strict_types=1);

namespace Tests\UnitTests\Actions;

use App\Payloads\LoginPayload;
use App\Domain\Results\LoginResult;
use App\Responders\LoginResponder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use App\Actions\LoginAction;
use App\Domain\Services\AuthenticationService;

class LoginActionTest extends TestCase
{
    // private MockObject|LoginResponder $responder;
    // private MockObject|AuthenticationService $service;
    // private MockObject|LoginPayload $payload;
    // private MockObject|Request $request;
    // private MockObject|Response $response;
    // private LoginAction $action;

    // protected function setUp(): void
    // {
    //     parent::setUp();
    //     $this->responder = $this->createMock(LoginResponder::class);
    //     $this->service = $this->createMock(AuthenticationService::class);
    //     $this->payload = $this->createMock(LoginPayload::class);
    //     $this->request = $this->createMock(Request::class);
    //     $this->response = $this->createMock(Response::class);

    //     $this->action = new LoginAction($this->payload, $this->responder, $this->service);
    // }

    // public function testInvokeHandlesNonPostRequest(): void
    // {
    //     // Arrange
    //     $this->payload->method('isPost')->willReturn(false);

    //     $this->service->expects($this->never())->method('login');

    //     $this->responder->expects($this->once())
    //         ->method('respond')
    //         ->with(
    //             $this->identicalTo($this->payload),
    //             $this->callback(function (LoginResult $result) {
    //                 return !$result->isSuccess();
    //             }),
    //             $this->identicalTo($this->response)
    //         )
    //         ->willReturn($this->response);

    //     // Act
    //     $actual_response = ($this->action)($this->request, $this->response);

    //     // Assert
    //     $this->assertSame($this->response, $actual_response);
    // }

    // public function testInvokeHandlesInvalidPostRequest(): void
    // {
    //     // Arrange
    //     $this->payload->method('isPost')->willReturn(true);
    //     $this->payload->method('isValid')->willReturn(false);

    //     $this->service->expects($this->never())->method('login');

    //     $this->responder->expects($this->once())
    //         ->method('respond')
    //         ->with(
    //             $this->identicalTo($this->payload),
    //             $this->callback(function (LoginResult $result) {
    //                 return !$result->isSuccess();
    //             }),
    //             $this->identicalTo($this->response)
    //         )
    //         ->willReturn($this->response);

    //     // Act
    //     $actual_response = ($this->action)($this->request, $this->response);

    //     // Assert
    //     $this->assertSame($this->response, $actual_response);
    // }

    // public function testInvokeHandlesValidPostRequest(): void
    // {
    //     // Arrange
    //     $this->payload->method('isPost')->willReturn(true);
    //     $this->payload->method('isValid')->willReturn(true);

    //     $expected_result = new LoginResult(true);

    //     $this->service->expects($this->once())
    //         ->method('login')
    //         ->with($this->identicalTo($this->payload))
    //         ->willReturn($expected_result);

    //     $this->responder->expects($this->once())
    //         ->method('respond')
    //         ->with(
    //             $this->identicalTo($this->payload),
    //             $this->identicalTo($expected_result),
    //             $this->identicalTo($this->response)
    //         )
    //         ->willReturn($this->response);

    //     // Act
    //     $actual_response = ($this->action)($this->request, $this->response);

    //     // Assert
    //     $this->assertSame($this->response, $actual_response);
    // }
}
