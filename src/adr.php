<?php

declare(strict_types=1);

use App\Actions\FaqAction;
use App\Actions\LoginAction;
use App\Utilities\Configuration;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Loader\FilesystemLoader;
use DI\Container;

// Composer Autoload
require __DIR__ . '/vendor/autoload.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------------------------
// 1. SETUP THE DEPENDENCY INJECTION CONTAINER (DI)
// -------------------------------------------------------------------------

$container = new Container();

// --- Container Definitions ---
$container->set(Configuration::class, function (ContainerInterface $c) {
    return new Configuration(require __DIR__ . '/settings.php');
});

// Configure Twig
$container->set(Twig::class, function (ContainerInterface $c) {
    $path = __DIR__ . '/templates';
    $loader = new FilesystemLoader($path);
    // In a production environment, you would enable caching.
    return new Twig($loader, ['cache' => false]);
});

// -------------------------------------------------------------------------
// 2. BOOTSTRAP THE SLIM APPLICATION
// -------------------------------------------------------------------------

AppFactory::setContainer($container);

$app = AppFactory::create();

// --- Middleware ---
// $app->add('csrf');
$app->add(TwigMiddleware::createFromContainer($app, Twig::class));
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// --- Routes ---
$app->get('/faq', FaqAction::class);
$app->get('/login', LoginAction::class);
$app->post('/login', LoginAction::class);

$app->run();