<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$pdo = db();
$user = current_user();
$game_id = (int)($_POST['game_id'] ?? 0);

if ($game_id > 0) {
    $st = $pdo->prepare("DELETE FROM user_games WHERE user_id = ? AND game_id = ?");
    $st->execute([(int)$user['id'], $game_id]);
}

header('Location: /lol-portal/profile.php');
exit;