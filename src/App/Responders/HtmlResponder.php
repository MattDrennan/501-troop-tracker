<?php

declare(strict_types=1);

namespace App\Responders;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class HtmlResponder
{
    /**
     * HtmlResponder constructor.
     *
     * @param Twig $view The Twig view service.
     */
    public function __construct(private Twig $view)
    {
    }

    /**
     * Renders an HTML template and returns the response.
     *
     * @param Response $response The PSR-7 response object.
     * @param string $template The name of the Twig template to render.
     * @param array $data An associative array of data to pass to the template.
     *
     * @return Response The modified PSR-7 response object with the rendered HTML.
     */
    public function respond(Response $response, string $template, array $data = []): Response
    {
        // The Twig service handles writing the HTML body and returns the response
        return $this->view->render($response, $template, $data);
    }
}