<?php

use App\Actions\FaqAction;
use App\Actions\LoginAction;
use App\Utilities\Container;
use App\Utilities\Router;
use App\Utilities\Configuration;
use App\Requests\HttpRequest;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Basic autoloader
spl_autoload_register(function ($class_name) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Composer Autoload
require 'vendor/autoload.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Dependency Injection Container Setup ---
$container = new Container();

// 1. Register services that cannot be autowired.
// The container can't guess that the Configuration class needs the settings.php file.
$container->set(Configuration::class, function () {
    return new Configuration(require __DIR__ . '/settings.php');
});

// The Request object should be a singleton created from globals for each request.
$container->set(HttpRequest::class, function () {
    return HttpRequest::createFromGlobals();
});

// Configure the Twig templating engine
$container->set(Environment::class, function () {
    $path = __DIR__ . '/templates';
    $loader = new FilesystemLoader($path);
    // In a production environment, you would enable caching.
    return new Environment($loader, ['cache' => false]);
});

// --- Routing and Action ---
$router = $container->get(Router::class);

// Define your application's routes.
// The router will match the request path against these definitions.
$router->addRoute('/faq', FaqAction::class);
$router->addRoute('/login', LoginAction::class);

$router->execute($container);