<?php

declare(strict_types=1);

namespace Tests\UnitTests\Actions;

use App\Actions\FaqAction;
use App\Responders\HtmlResponder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function PHPUnit\Framework\identicalTo;

class FaqActionTest extends TestCase
{
    private MockObject|HtmlResponder $responder;
    private FaqAction $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = $this->createMock(HtmlResponder::class);
        $this->subject = new FaqAction($this->responder);
    }

    public function testInvokeCallsResponderAndReturnsResponse(): void
    {
        //  Arrange
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $this->responder->expects($this->once())
            ->method('respond')
            ->with($response, 'pages/faq.html')
            ->willReturn($response);

        //  Act
        $actual_response = ($this->subject)($request, $response);

        //  Assert
        $this->assertSame($response, $actual_response);
    }
}