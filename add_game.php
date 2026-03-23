<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$pdo = db();
$user = current_user();
$game_id = (int)($_POST['game_id'] ?? 0);

if ($game_id <= 0) {
    header('Location: /lol-portal/games.php');
    exit;
}

$st = $pdo->prepare("SELECT id FROM user_games WHERE user_id = ? AND game_id = ?");
$st->execute([(int)$user['id'], $game_id]);

if (!$st->fetch()) {
    $st = $pdo->prepare("
        INSERT INTO user_games (user_id, game_id, is_favorite, play_time_hours)
        VALUES (?, ?, 0, FLOOR(RAND()*200))
    ");
    $st->execute([(int)$user['id'], $game_id]);
}

header('Location: /lol-portal/profile.php');
exit;