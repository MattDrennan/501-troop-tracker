<?php

declare(strict_types=1);

namespace Tests\UnitTests\Responders;

use App\Domain\Results\LoginResult;
use App\Responders\LoginResponder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\StreamInterface as Stream;


class LoginResponderTest extends TestCase
{
    private MockObject|Twig $view;
    private LoginResponder $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->view = $this->createMock(Twig::class);
        $this->subject = new LoginResponder($this->view);
    }

    public function testRespondSuccessHtml(): void
    {
        // Arrange        
        $result = $this->createMock(LoginResult::class);
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $response_with_header = $this->createMock(Response::class);
        $response_with_status = $this->createMock(Response::class);

        $request->expects($this->once())
            ->method('getHeaderLine')
            ->with('Accept')
            ->willReturn('html');

        $result->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);

        $response->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/dashboard')
            ->willReturn($response_with_header);

        $response_with_header->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturn($response_with_status);

        // Act
        $actual_response = $this->subject->respond($request, $response, $result);

        // Assert
        $this->assertSame($response_with_status, $actual_response);
    }

    public function testRespondSuccessApi(): void
    {
        // Arrange
        $request = $this->createMock(Request::class);
        $result = $this->createMock(LoginResult::class);
        $response = $this->createMock(Response::class);
        $response_with_header = $this->createMock(Response::class);
        $response_with_status = $this->createMock(Response::class);
        $stream = $this->createMock(Stream::class);
        $payload = ['sample' => 'x'];

        $request->expects($this->once())
            ->method('getHeaderLine')
            ->with('Accept')
            ->willReturn('application/json');

        $result->expects($this->once())
            ->method('getDataPayload')
            ->willReturn($payload);

        $result->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);

        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $stream->expects($this->once())
            ->method('write')
            ->with(json_encode($payload));

        $response->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturn($response_with_header);

        $response_with_header->expects($this->once())
            ->method('withStatus')
            ->with(200)
            ->willReturn($response_with_status);

        // Act
        $actual_response = $this->subject->respond($request, $response, $result);

        // Assert
        $this->assertSame($response_with_status, $actual_response);
    }

    public function testRespondFailureHtml(): void
    {
        // Arrange        
        $result = $this->createMock(LoginResult::class);
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $payload = ['sample' => 'x'];

        $request->expects($this->once())
            ->method('getHeaderLine')
            ->with('Accept')
            ->willReturn('html');

        $result->expects($this->once())
            ->method('isSuccess')
            ->willReturn(false);

        $result->expects($this->once())
            ->method('getDataPayload')
            ->willReturn($payload);

        $this->view->expects($this->once())
            ->method('render')
            ->with($response, 'pages/login.html', $payload)
            ->willReturn($response);

        // Act
        $actual_response = $this->subject->respond($request, $response, $result);

        // Assert
        $this->assertSame($response, $actual_response);
    }
    public function testRespondFailureApi(): void
    {
        // Arrange        
        $result = $this->createMock(LoginResult::class);
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $response_with_header = $this->createMock(Response::class);
        $response_with_status = $this->createMock(Response::class);
        $stream = $this->createMock(Stream::class);
        $payload = ['sample' => 'x'];

        $request->expects($this->once())
            ->method('getHeaderLine')
            ->with('Accept')
            ->willReturn('application/json');

        $result->expects($this->once())
            ->method('isSuccess')
            ->willReturn(false);

        $result->expects($this->once())
            ->method('getDataPayload')
            ->willReturn($payload);

        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $stream->expects($this->once())
            ->method('write')
            ->with(json_encode($payload));

        $response->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturn($response_with_header);

        $response_with_header->expects($this->once())
            ->method('withStatus')
            ->with(401)
            ->willReturn($response_with_status);

        // Act
        $actual_response = $this->subject->respond($request, $response, $result);

        // Assert
        $this->assertSame($response_with_status, $actual_response);
    }
}
