<?php

declare(strict_types=1);

namespace App\Actions;

use App\Responders\HtmlResponder;

class FaqAction
{
    public function __construct(private readonly HtmlResponder $responder)
    {
    }

    public function execute(): void
    {
        $this->responder->render('pages/faq.html');
    }
}

