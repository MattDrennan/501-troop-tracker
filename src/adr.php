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

if (!file_exists(__DIR__ . '/settings.php')) {
    die('Missing settings.php');
}

$container = new Container();

// --- Container Definitions ---
$container->set(Configuration::class, function (ContainerInterface $c) {
    return new Configuration(require __DIR__ . '/settings.php');
});

$container->set(PDO::class, function (ContainerInterface $c) {
    $config = $c->get(Configuration::class);
    $settings = $config->get('db');
    $dsn = "mysql:host={$settings['host']};dbname={$settings['name']};";
    $pdo = new PDO($dsn, $settings['user'], $settings['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $pdo;
});

// Configure Twig
$container->set(Twig::class, function (ContainerInterface $c) {
    $path = __DIR__ . '/templates';
    $loader = new FilesystemLoader($path);

    $config = $c->get(Configuration::class);

    // In a production environment, you would enable caching.
    $twig = new Twig($loader, ['cache' => $config->get('twig.cache', false)]);

    $twig->getEnvironment()->addGlobal('config', $config);

    return $twig;
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

$app->setBasePath('/troop-tracker');

// --- Routes ---
$app->get('/faq', FaqAction::class)->setName('faq');
$app->get('/login', LoginAction::class)->setName('login');
$app->post('/login', LoginAction::class);

// -------------------------------------------------------------------------
// 3. AUTOBOTS ROLLOUT!
// -------------------------------------------------------------------------

$app->run();