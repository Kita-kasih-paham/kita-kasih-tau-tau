<?php

session_start();

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/Middleware.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Validator.php';
require_once __DIR__ . '/core/Flash.php';
require_once __DIR__ . '/core/helpers.php';

// Autoload
spl_autoload_register(function (string $class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) require_once $file;
});

$router = new Core\Router();

require_once __DIR__ . '/routes/web.php';

$router->dispatch();
