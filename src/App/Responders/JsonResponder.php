<?php

declare(strict_types=1);

namespace App\Responders;

use Psr\Http\Message\ResponseInterface as Response;

class JsonResponder
{
    /**
     * Respond with JSON.
     *
     * @param Response $response The response object.
     * @param array|null $data The data to encode as JSON.
     * @return Response The modified response object.
     */

    public function respond(Response $response, array $data = null, int $status = 200): Response
    {
        if (isset($data)) {
            $response->getBody()->write(json_encode($data));
        }
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}