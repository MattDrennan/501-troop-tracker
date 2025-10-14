<?php

declare(strict_types=1);

spl_autoload_register(function (string $class_name): void {
    $project_root = dirname(__DIR__, 1);

    // Map 'App' namespace to 'src' directory
    if (strpos($class_name, 'App\\') === 0) {
        $file = $project_root . '/src/' . str_replace('\\', '/', $class_name) . '.php';
    }
    // Map 'Tests' namespace to 'tests' directory
    else if (strpos($class_name, 'Tests\\') === 0) {
        $file = $project_root . '/' . str_replace('\\', '/', $class_name) . '.php';
    }

    if (isset($file) && file_exists($file)) {
        require_once $file;
    }
});