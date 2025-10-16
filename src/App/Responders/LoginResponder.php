<?php

declare(strict_types=1);

namespace App\Responders;

use App\Domain\Results\LoginResult;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Slim\Views\Twig;

class LoginResponder
{
    use RequestableTrait;

    // Inject the Twig View service
    public function __construct(private Twig $view)
    {
    }

    /**
     * Builds the HTTP Response for the login action, returning JSON or HTML.
     */
    public function respond(Request $request, Response $response, LoginResult $result): Response
    {
        $is_api = $this->expectsJson($request);

        $payload = array_merge($result->getDataPayload());

        if ($result->isSuccess()) {
            if ($is_api) {
                $response->getBody()->write(json_encode($payload));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }

            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }

        $status = empty($errors) ? 401 : 400;

        if ($is_api) {
            // JSON: Return 400/401 and the payload
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
        }

        // The Twig service handles writing the HTML body and returns the response
        return $this->view->render($response, 'pages/login.html', $payload);
    }
}