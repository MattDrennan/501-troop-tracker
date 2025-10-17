<?php

declare(strict_types=1);

namespace Tests\UnitTests\Payloads;

use App\Payloads\LoginPayload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ServerRequestFactory;

class LoginPayloadTest extends TestCase
{
    public function testIsValidReturnsTrueWithValidData(): void
    {
        // Arrange: Create a mock PSR-7 request with valid POST data
        $data = ['tkid' => 'TK-12345', 'password' => 'password123'];

        // Act: Instantiate the LoginPayload with the mock request
        $subject = new LoginPayload($data);

        // Assert: The request should be valid and have no errors
        $this->assertTrue($subject->isValid());
        $this->assertEmpty($subject->getErrors());
        $this->assertEquals('TK-12345', $subject->getUsername());
        $this->assertEquals('password123', $subject->getPassword());
        $this->assertFalse($subject->stayLoggedIn());
    }

    public function testIsValidReturnsFalseWithMissingTkId(): void
    {
        // Arrange: Create a request with a missing tkid
        $data = ['password' => 'password123'];

        // Act
        $subject = new LoginPayload($data);
        $errors = $subject->getErrors();

        // Assert: The request should be invalid
        $this->assertFalse($subject->isValid());
        $this->assertArrayHasKey('tk_id', $errors);
    }

    public function testIsValidReturnsFalseWithEmptyPassword(): void
    {
        // Arrange: Create a request with an empty password
        $data = ['tkid' => 'TK-12345', 'password' => ''];

        // Act
        $subject = new LoginPayload($data);

        // Assert
        $this->assertFalse($subject->isValid());
        $this->assertArrayHasKey('password', $subject->getErrors());
    }

    protected function createMockRequest(array $postData = []): ServerRequestInterface
    {
        // 1. Create a basic Request object
        $data = (new ServerRequestFactory())->createServerRequest('POST', '/login');

        // 2. Set the POST data (the parsed body)
        $data = $data->withParsedBody($postData);

        return $data;
    }
}
