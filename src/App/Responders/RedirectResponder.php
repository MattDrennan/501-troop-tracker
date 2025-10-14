<?php

declare(strict_types=1);

namespace App\Responders;

class RedirectResponder
{
    public function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit();
    }

    public function redirectWithMessage(string $url, string $message): void
    {
        $_SESSION['message'] = $message;
        $this->redirect($url);
    }
}