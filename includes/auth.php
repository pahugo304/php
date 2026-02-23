<?php
declare(strict_types=1);

function is_logged_in(): bool
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']);
}

function current_user(): ?array
{
    return is_logged_in() ? $_SESSION['user'] : null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /lol-portal/login.php');
        exit;
    }
}

function is_admin(): bool
{
    return is_logged_in() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        echo "403 Forbidden";
        exit;
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(?string $token): void
{
    if (!$token || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(400);
        echo "Bad Request (CSRF)";
        exit;
    }
}
