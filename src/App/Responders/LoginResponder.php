<?php

declare(strict_types=1);

namespace App\Responders;

use App\Domain\Responses\LoginResponse;
use Twig\Environment;

/**
 * 
 */
class LoginResponder extends HtmlResponder
{
    public function __construct(
        private readonly RedirectResponder $redirectResponder,
        Environment $twig
    ) {
        parent::__construct($twig);
    }

    public function send(?LoginResponse $response = null): void
    {
        if ($response === null || !$response->isSuccess()) {
            $this->render('x');
        }

        $this->redirectResponder->redirect('index.php');
    }
}