<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AuthenticationInterface;
use App\Contracts\ForumInterface;
use App\Enums\AuthenticationStatus;
use Illuminate\Support\Facades\Http;

/**
 * Service to interact with the Xenforo API.
 */
class XenforoService implements AuthenticationInterface, ForumInterface
{
    public function getAvatarUrl(mixed $forum_user_id): string
    {
        $message = $this->getUserMessage($forum_user_id);

        return $message->user->avatar_urls->l ?? '';
    }

    private function getUserMessage(mixed $forum_user_id): mixed
    {
        $headers = [
            'XF-Api-Key' => config('tracker.plugins.xenforo.key'),
            'XF-Api-User' => config('tracker.plugins.xenforo.user'),
        ];

        $url = config('tracker.plugins.xenforo.apiurl') . "/users/$forum_user_id";

        $response = Http::withHeaders($headers)->get($url);

        $message = json_decode($response->body(), false);

        return $message;
    }

    /**
     * Authenticates a user against the Xenforo API.
     *
     * @param string $username The user's forum username.
     * @param string $password The user's password.
     * @return AuthenticationStatus The result of the authentication attempt.
     */
    public function authenticate(string $username, string $password): AuthenticationStatus
    {
        $message = $this->getAuthenicationMessage($username, $password);

        if (isset($message) && isset($message->success) && $message->success)
        {
            if (isset($message->user->is_banned) && $message->user->is_banned)
            {
                //  banned flag in the message
                return AuthenticationStatus::BANNED;
            }

            return AuthenticationStatus::SUCCESS;
        }

        //  no idea but don't let them in
        return AuthenticationStatus::FAILURE;
    }

    /**
     * Verifies a user against the Xenforo API.
     *
     * @param string $username The user's forum username.
     * @param string $password The user's password.
     * @return mixed The identifier the verification attempt or null if it fails.
     */
    public function verify(string $username, string $password): mixed
    {
        $message = $this->getAuthenicationMessage($username, $password);

        if (isset($message) && isset($message->success) && $message->success)
        {
            if (isset($message->user->is_banned) && $message->user->is_banned)
            {
                return null;
            }

            return $message->user->user_id;
        }

        //  no idea but don't let them in
        return null;
    }

    private function getAuthenicationMessage(string $username, string $password): mixed
    {
        $credentials = [
            'login' => $username,
            'password' => $password,
        ];

        $headers = [
            'XF-Api-Key' => config('tracker.plugins.xenforo.key'),
            'XF-Api-User' => config('tracker.plugins.xenforo.user'),
        ];

        $url = config('tracker.plugins.xenforo.apiurl') . '/auth';

        $response = Http::withHeaders($headers)->post($url, $credentials);

        $message = json_decode($response->body(), false);

        return $message;
    }
}