<?php

use App\Actions\LoginAction;
use App\Domain\Services\AuthenticationService;
use App\Utilities\Container;
use App\Utilities\Configuration;
use App\Utilities\DatabaseConnection;
use App\Utilities\Request;
use App\Responders\LoginResponder;
use App\Responders\RedirectResponder;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Basic autoloader
spl_autoload_register(function ($class_name) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

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
$container->set(Request::class, function () {
    return Request::createFromGlobals();
});

// Configure the Twig templating engine
$container->set(Environment::class, function () {
    $loader = new FilesystemLoader(__DIR__ . '/../templates');
    // In a production environment, you would enable caching.
    return new Environment($loader, ['cache' => false]);
});

// 2. Any legacy functions needed by the new classes should be included here.
// As you refactor more, this list will shrink.
require_once 'config.php';

// --- Routing and Action ---
$action = $_GET['action'] ?? 'home';

if ($action === 'login') {
    // The container will now automatically resolve LoginAction and all of its
    // dependencies (AuthenticationService, LoginResponder, etc.) recursively.
    $loginAction = $container->get(LoginAction::class);
    ($loginAction)();
}
// Future actions like 'logout', 'register', etc., can be added here.