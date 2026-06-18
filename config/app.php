<?php

// Load .env
$env = [];
if (file_exists(__DIR__ . '/../.env')) {
    foreach (file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        [$key, $value] = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
}

define('APP_NAME', $env['APP_NAME'] ?? 'Sistem Pengelolaan Stok');
define('APP_ENV',  $env['APP_ENV']  ?? 'local');
define('APP_URL',  $env['APP_URL']  ?? 'http://localhost/');

define('DB_HOST', $env['DB_HOST'] ?? '127.0.0.1');
define('DB_PORT', $env['DB_PORT'] ?? '3306');
define('DB_NAME', $env['DB_NAME'] ?? 'sistem_pengelolaan_stok_db');
define('DB_USER', $env['DB_USER'] ?? 'root');
define('DB_PASS', $env['DB_PASS'] ?? '');
