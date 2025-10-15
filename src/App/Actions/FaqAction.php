<?php

declare(strict_types=1);

namespace App\Actions;

use App\Responders\FaqResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

class FaqAction
{
    public function __construct(
        private readonly FaqResponder $responder
    ) {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->responder->respond($request, $response);
    }
}
