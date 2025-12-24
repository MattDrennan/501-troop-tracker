<?php

declare(strict_types=1);

namespace App\Contracts;

interface ForumInterface
{
    public function getAvatarUrl(mixed $forum_user_id): string;
}