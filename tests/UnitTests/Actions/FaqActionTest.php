<?php

declare(strict_types=1);

namespace Tests\UnitTests\Actions;

use App\Actions\FaqAction;
use App\Responders\FaqResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class FaqActionTest extends TestCase
{
    // private MockObject|FaqResponder $responder;
    // private MockObject|Request $request;
    // private MockObject|Response $response;

    // protected function setUp(): void
    // {
    //     parent::setUp();

    //     $this->responder = $this->createMock(FaqResponder::class);
    //     $this->request = $this->createMock(Request::class);
    //     $this->response = $this->createMock(Response::class);
    // }

    // public function testInvokeCallsResponderAndReturnsResponse(): void
    // {
    //     //  arrange
    //     $action = $this->createFaqAction();

    //     $this->responder->expects($this->once())
    //         ->method('respond')
    //         ->with($this->identicalTo($this->response))
    //         ->willReturn($this->response);

    //     //  act
    //     $actual_response = $action($this->request, $this->response);

    //     //  assert
    //     $this->assertSame($this->response, $actual_response);
    // }

    // private function createFaqAction(): FaqAction
    // {
    //     return new FaqAction($this->responder);
    // }
}