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
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $response_with_header = $this->createMock(Response::class);
        $response_with_status = $this->createMock(Response::class);

        $request->method('getHeaderLine')->with('Accept')->willReturn('html');

        $response->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/dashboard')
            ->willReturn($response_with_header);

        $response_with_header->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturn($response_with_status);

        // $this->view->method('render')->with($this->response, 'pages/login.html', )->willReturn($this->response);

        $result = $this->createMock(LoginResult::class);
        $result->method('isSuccess')->willReturn(true);

        // Act
        $actual_response = $this->subject->respond($request, $response, $result);

        // Assert
        $this->assertSame($response_with_status, $actual_response);
    }

    // public function testRespondSuccessApi(): void
    // {
    //     // Arrange
    //     $request = $this->createMock(Requestable::class);
    //     $request->method('expectsJson')->willReturn(true);

    //     $result_payload = ['user' => ['id' => 1, 'name' => 'Test User']];
    //     $result = $this->createMock(LoginResult::class);
    //     $result->method('isSuccess')->willReturn(true);
    //     $result->method('getDataPayload')->with('password')->willReturn($result_payload);

    //     // Act
    //     $response = new Response();
    //     $actual_response = $this->responder->respond($request, $this->response, $result);
    //     $actual_response->getBody()->rewind();
    //     $body = $actual_response->getBody()->getContents();

    //     // Assert
    //     $this->assertEquals(200, $actual_response->getStatusCode());
    //     $this->assertEquals('application/json', $actual_response->getHeaderLine('Content-Type'));
    //     $this->assertJsonStringEqualsJsonString(json_encode($result_payload), $body);
    // }

    // public function testRespondFailureHtml(): void
    // {
    //     // Arrange
    //     $request_payload = ['username' => 'baduser'];
    //     $request = $this->createMock(Requestable::class);
    //     $request->method('expectsJson')->willReturn(false);
    //     $request->method('getDataPayload')->willReturn($request_payload);

    //     $result_payload = ['error' => 'Invalid credentials'];
    //     $result = $this->createMock(LoginResult::class);
    //     $result->method('isSuccess')->willReturn(false);
    //     $result->method('getDataPayload')->with('password')->willReturn($result_payload);

    //     $response = new Response();

    //     $expected_view_data = array_merge($result_payload, $request_payload);
    //     $this->view->expects($this->once())
    //         ->method('render')
    //         ->with(
    //             $this->identicalTo($response),
    //             'login/login.twig',
    //             $expected_view_data
    //         )
    //         ->willReturn($response);

    //     // Act
    //     $this->responder->respond($request, $this->response, $result);
    // }

    // public function testRespondFailureApi(): void
    // {
    //     // Arrange
    //     $request = $this->createMock(Requestable::class);
    //     $request->method('expectsJson')->willReturn(true);

    //     $result_payload = ['error' => 'Invalid credentials'];
    //     $result = $this->createMock(LoginResult::class);
    //     $result->method('isSuccess')->willReturn(false);
    //     $result->method('getDataPayload')->with('password')->willReturn($result_payload);

    //     // Act
    //     $response = new Response();
    //     $actual_response = $this->responder->respond($request, $this->response, $result);
    //     $actual_response->getBody()->rewind();
    //     $body = $actual_response->getBody()->getContents();

    //     // Assert
    //     $this->assertEquals(401, $actual_response->getStatusCode());
    //     $this->assertEquals('application/json', $actual_response->getHeaderLine('Content-Type'));
    //     $this->assertJsonStringEqualsJsonString(json_encode($result_payload), $body);
    // }

    // public function testRespondFailureApiSets400StatusForValidationError(): void
    // {
    //     // Arrange
    //     $request = $this->createMock(Requestable::class);
    //     $request->method('expectsJson')->willReturn(true);

    //     // Simulate a payload that might come from a validation failure
    //     $result_payload = ['errors' => ['username' => 'Username is required']];
    //     $result = $this->createMock(LoginResult::class);
    //     $result->method('isSuccess')->willReturn(false);
    //     $result->method('getDataPayload')->with('password')->willReturn($result_payload);

    //     // Act
    //     $response = new Response();
    //     $actual_response = $this->responder->respond($request, $this->response, $result);
    //     $actual_response->getBody()->rewind();
    //     $body = $actual_response->getBody()->getContents();

    //     // Assert: A payload with an 'errors' key should result in a 400 status.
    //     $this->assertEquals(400, $actual_response->getStatusCode());
    //     $this->assertEquals('application/json', $actual_response->getHeaderLine('Content-Type'));
    //     $this->assertJsonStringEqualsJsonString(json_encode($result_payload), $body);
    // }
}
