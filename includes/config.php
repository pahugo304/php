<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/../.env')) {
    foreach (file(__DIR__ . '/../.env') as $line) {
        if (trim($line) === '' || str_starts_with(trim($line), '#')) continue;
        [$k, $v] = explode('=', trim($line), 2);
        $_ENV[$k] = $v;
    }
}

function env(string $key, string $default = ''): string {
    return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
}

const DB_HOST = '127.0.0.1';
const DB_NAME = 'lol_portal';

define('DB_USER', env('DB_USER'));
define('DB_PASS', env('DB_PASS'));

ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');

session_name('LOLSESSID');
session_start();