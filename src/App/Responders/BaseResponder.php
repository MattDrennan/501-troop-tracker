<?php

declare(strict_types=1);

namespace App\Responders;

use App\Results\RedirectResult;

class BaseResponder
{
    public function redirect(string $url): RedirectResult
    {
        return new RedirectResult($url);
    }

    public function redirectWithMessage(string $url, string $message): RedirectResult
    {
        $_SESSION['message'] = $message;
        return $this->redirect($url);
    }
}