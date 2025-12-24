<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Organization;
use App\Services\FlashMessageService;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class FlashMessageServiceTest extends TestCase
{
    public function test_success_adds_message_to_session(): void
    {
        // Arrange
        $message = 'Operation was successful.';
        Session::shouldReceive('get')
            ->once()
            ->with('flash_messages', [])
            ->andReturn([]);

        Session::shouldReceive('put')
            ->once()
            ->with('flash_messages', ['success' => [$message]]);

        $subject = new FlashMessageService();

        // Act
        $subject->success($message);

        // Assert (handled by mock expectations)
    }

    public function test_warning_adds_message_to_session(): void
    {
        // Arrange
        $message = 'This is a warning.';
        Session::shouldReceive('get')
            ->once()
            ->with('flash_messages', [])
            ->andReturn([]);

        Session::shouldReceive('put')
            ->once()
            ->with('flash_messages', ['warning' => [$message]]);

        $subject = new FlashMessageService();

        // Act
        $subject->warning($message);

        // Assert (handled by mock expectations)
    }

    public function test_danger_adds_message_to_session(): void
    {
        // Arrange
        $message = 'An error occurred.';
        Session::shouldReceive('get')
            ->once()
            ->with('flash_messages', [])
            ->andReturn([]);

        Session::shouldReceive('put')
            ->once()
            ->with('flash_messages', ['danger' => [$message]]);

        $subject = new FlashMessageService();

        // Act
        $subject->danger($message);

        // Assert (handled by mock expectations)
    }

    public function test_created_adds_model_based_message(): void
    {
        // Arrange
        $model = new Organization(['name' => 'Test Org']);
        $expected_message = 'Organization "Test Org" was created successfully.';

        Session::shouldReceive('get')->once()->andReturn([]);
        Session::shouldReceive('put')
            ->once()
            ->with('flash_messages', ['success' => [$expected_message]]);

        $subject = new FlashMessageService();

        // Act
        $subject->created($model);

        // Assert (handled by mock expectations)
    }

    public function test_updated_adds_model_based_message(): void
    {
        // Arrange
        $model = new Organization(['name' => 'Test Org']);
        $expected_message = 'Organization "Test Org" was updated successfully.';

        Session::shouldReceive('get')->once()->andReturn([]);
        Session::shouldReceive('put')
            ->once()
            ->with('flash_messages', ['success' => [$expected_message]]);

        $subject = new FlashMessageService();

        // Act
        $subject->updated($model);

        // Assert (handled by mock expectations)
    }

    public function test_deleted_adds_model_based_message(): void
    {
        // Arrange
        $model = new Organization(['name' => 'Test Org']);
        $expected_message = 'Organization "Test Org" was deleted successfully.';

        Session::shouldReceive('get')->once()->andReturn([]);
        Session::shouldReceive('put')
            ->once()
            ->with('flash_messages', ['success' => [$expected_message]]);

        $subject = new FlashMessageService();

        // Act
        $subject->deleted($model);

        // Assert (handled by mock expectations)
    }

    public function test_get_messages_retrieves_and_removes_messages_from_session(): void
    {
        // Arrange
        $messages = ['success' => ['It worked!']];
        Session::shouldReceive('get')
            ->once()
            ->with('flash_messages', [])
            ->andReturn($messages);

        Session::shouldReceive('remove')->once()->with('flash_messages');

        $subject = new FlashMessageService();

        // Act
        $retrieved_messages = $subject->getMessages();

        // Assert
        $this->assertEquals($messages, $retrieved_messages);
    }
}