<?php

declare(strict_types=1);

namespace App\Responders;

use App\Results\HtmlResult;
use Twig\Environment;

/**
 * An HTML responder for sending HTML responses using Twig.
 */
class HtmlResponder extends BaseResponder
{
    public function __construct(private readonly Environment $twig)
    {
    }

    /**
     * Renders a Twig template and sends the response to the browser.
     *
     * @param array<string, mixed> $data
     */
    public function render(string $template_name, array $data = []): HtmlResult
    {
        $html = $this->twig->render($template_name, $data);

        return new HtmlResult($html);
    }
}