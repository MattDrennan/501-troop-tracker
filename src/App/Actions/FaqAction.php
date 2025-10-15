<?php

declare(strict_types=1);

namespace App\Actions;

use App\Responders\HtmlResponder;
use App\Results\ActionResult;
use App\Results\HtmlResult;

class FaqAction implements ActionInterface
{
    public function __construct(private readonly HtmlResponder $responder)
    {
    }

    public function execute(): HtmlResult|ActionResult
    {
        return $this->responder->render('pages/faq.html');
    }
}

