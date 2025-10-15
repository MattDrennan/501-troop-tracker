<?php

declare(strict_types=1);

namespace App\Results;

class RedirectResult implements ActionResult
{
    public function __construct(private readonly string $url)
    {
    }

    public function send(): void
    {
        header('Location: ' . $this->url);
        exit();
    }
}

