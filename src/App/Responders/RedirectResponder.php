<?php

declare(strict_types=1);

namespace App\Responders;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * Responder for handling HTTP redirects.
 */
class RedirectResponder
{
    /**
     * Responds with an HTTP redirect.
     *
     * @param Response $response The PSR-7 response object.
     * @param string $location The URL to redirect to.
     * @return Response The modified PSR-7 response object with redirect headers.
     */
    public function respond(Response $response, string $location): Response
    {
        return $response->withHeader('Location', $location)->withStatus(302);
    }
}