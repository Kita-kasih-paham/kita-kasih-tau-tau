<?php

namespace Core;

class Flash
{
    public static function set(string $type, string $message): void
    {
        $_SESSION['_flash'][$type] = $message;
    }

    public static function success(string $msg): void { self::set('success', $msg); }
    public static function error(string $msg): void   { self::set('error',   $msg); }

    public static function get(string $type): ?string
    {
        $msg = $_SESSION['_flash'][$type] ?? null;
        unset($_SESSION['_flash'][$type]);
        return $msg;
    }

    public static function has(string $type): bool
    {
        return isset($_SESSION['_flash'][$type]);
    }

    /** Store validation errors + old input for redirect-back */
    public static function setErrors(array $errors, array $old = []): void
    {
        $_SESSION['_flash']['errors'] = $errors;
        $_SESSION['_flash']['old']    = $old;
    }

    public static function getErrors(): array
    {
        $e = $_SESSION['_flash']['errors'] ?? [];
        unset($_SESSION['_flash']['errors']);
        return $e;
    }

    public static function getOld(): array
    {
        $o = $_SESSION['_flash']['old'] ?? [];
        unset($_SESSION['_flash']['old']);
        return $o;
    }
}
