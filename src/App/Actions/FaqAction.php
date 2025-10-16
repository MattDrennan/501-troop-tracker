<?php

declare(strict_types=1);

namespace App\Actions;

use App\Responders\HtmlResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FaqAction
{
    public function __construct(
        private readonly HtmlResponder $responder
    ) {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->responder->respond($response, 'pages/faq.html');
    }
}
