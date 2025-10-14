<?php

declare(strict_types=1);

namespace App\Responders;

use Twig\Environment;

/**
 * An abstract base responder for sending HTML responses using Twig.
 */
abstract class HtmlResponder
{
    public function __construct(protected readonly Environment $twig)
    {
    }

    /**
     * Renders a Twig template and sends the response to the browser.
     *
     * @param array<string, mixed> $data
     */
    public function render(string $template_name, array $data = []): void
    {
        $this->sendHeaders();
        echo $this->twig->render($template_name, $data);
        exit();
    }

    /**
     * Sends the common HTTP headers for an HTML response.
     */
    protected function sendHeaders(): void
    {
        header('Content-Type: text/html; charset=UTF-8');
    }
}