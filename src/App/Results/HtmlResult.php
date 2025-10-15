<?php

declare(strict_types=1);

namespace App\Results;

class HtmlResult implements ActionResult
{
    public function __construct(private readonly string $html)
    {
    }

    public function send(): void
    {
        header('Content-Type: text/html; charset=UTF-8');
        echo $this->html;
        exit();
    }
}
