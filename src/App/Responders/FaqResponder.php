<?php

declare(strict_types=1);

namespace App\Responders;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Slim\Views\Twig;

class FaqResponder
{
    // Inject the Twig View service
    public function __construct(private Twig $view)
    {
    }

    /**
     * Builds the HTTP Response for the login action, returning JSON or HTML.
     */
    public function respond(Request $request, Response $response): Response
    {
        // The Twig service handles writing the HTML body and returns the response
        return $this->view->render($response, 'pages/faq.html');
    }
}