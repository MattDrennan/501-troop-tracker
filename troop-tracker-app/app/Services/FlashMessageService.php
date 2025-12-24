<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

/**
 * Manages flash messages stored in the session.
 * Flash messages are messages that are stored for one subsequent request.
 */
class FlashMessageService
{
    /**
     * The session key for storing flash messages.
     *
     * @var string
     */
    private const FLASH_KEY = 'flash_messages';

    /**
     * Adds a success flash message on a created model.
     *
     * @param Model $model The model that was created.
     */
    public function created(Model $model): void
    {
        $this->addModelMessage($model, 'created');
    }

    /**
     * Adds a success flash message on a updated model.
     *
     * @param Model $model The model that was updated.
     */
    public function updated(Model $model): void
    {
        $this->addModelMessage($model, 'updated');
    }

    /**
     * Adds a success flash message on a deleted model.
     *
     * @param Model $model The model that was deleted.
     */
    public function deleted(Model $model): void
    {
        $this->addModelMessage($model, 'deleted');
    }

    /**
     * Builds and adds a flash message for a model action (created, updated, deleted).
     *
     * @param Model $model The model instance for which to create the message.
     * @param string $action The action performed on the model (e.g., 'created').
     */
    private function addModelMessage(Model $model, string $action): void
    {
        // Get the base class name (e.g. "Organization")
        $object_name = class_basename($model);

        // If the model has a "name" attribute, include it
        $display_name = $model->getAttribute('name');

        // Build the message
        $message = $object_name;

        if (!empty($display_name))
        {
            $message .= " \"{$display_name}\"";
        }

        $message .= " was {$action} successfully.";

        $this->addMessage('success', $message);
    }

    /**
     * Adds a success flash message.
     *
     * @param string $message The message content.
     */
    public function success(string $message): void
    {
        $this->addMessage('success', $message);
    }

    /**
     * Adds a warning flash message.
     *
     * @param string $message The message content.
     */
    public function warning(string $message): void
    {
        $this->addMessage('warning', $message);
    }

    /**
     * Adds a danger/error flash message.
     *
     * @param string $message The message content.
     */
    public function danger(string $message): void
    {
        $this->addMessage('danger', $message);
    }

    /**
     * Adds a message to the session flash data.
     *
     * @param string $type The type of message (e.g., 'success', 'warning').
     * @param string $message The message content.
     */
    private function addMessage(string $type, string $message): void
    {
        $messages = Session::get(self::FLASH_KEY, []);

        $messages[$type][] = $message;

        Session::put(self::FLASH_KEY, $messages);
    }

    /**
     * Retrieves all flash messages from the session.
     *
     * @return array<string, array<string>> An associative array where keys are message types
     *                                      and values are arrays of messages.
     *                                      Example: ['success' => ['Profile updated!']]
     */
    public function getMessages(): array
    {
        $messages = Session::get(self::FLASH_KEY, []);

        Session::remove(self::FLASH_KEY);

        return $messages;
    }

}