<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Defines the types of notifications that can be displayed to a user.
 *
 * This is typically used for flash messages or alerts to convey information
 * with a certain level of importance or context (e.g., success, error).
 */
enum NoticeType: string
{
    use HasEnumHelpers;

    /**
     * For informational messages.
     */
    case Info = 'info';
    /**
     * For success messages (e.g., after a form is submitted correctly).
     */
    case Success = 'success';
    /**
     * For warnings that require user attention.
     */
    case Warning = 'warning';
    /**
     * For critical errors or failure messages.
     */
    case Danger = 'danger';

    /**
     * Summary of toDescriptions
     * @return array{danger: string, info: string, success: string, warning: string}
     */
    public static function toDescriptions(): array
    {
        return [
            'info' => 'NOW HEAR THIS!',
            'success' => 'MISSION ACCOMPLISHED!',
            'warning' => 'ATTENTION TROOPERS!',
            'danger' => 'BATTLE STATIONS!'
        ];
    }
}