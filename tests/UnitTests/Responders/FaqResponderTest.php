<?php

declare(strict_types=1);

namespace Tests\UnitTests\Responders;

use App\Responders\FaqResponder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Slim\Views\Twig;

class FaqResponderTest extends TestCase
{
    private MockObject|Twig $view;
    private FaqResponder $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->view = $this->createMock(Twig::class);
        $this->subject = new FaqResponder($this->view);
    }

    public function testRespondRendersFaqPageAndReturnsResponse(): void
    {
        // Arrange
        $response = $this->createMock(Response::class);
        $request = $this->createMock(Request::class);

        $this->view->expects($this->once())
            ->method('render')
            ->with($this->identicalTo($response), 'pages/faq.html')
            ->willReturn($response);

        // Act
        $actual_response = $this->subject->respond($request, $response);

        // Assert
        $this->assertSame($response, $actual_response);
    }
}