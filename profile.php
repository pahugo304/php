<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

require_login();
$user = current_user();
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Profil</title></head>
<body>
<h1>Profil</h1>

<p>Username: <strong><?= htmlspecialchars($user['username']) ?></strong></p>
<p>Email: <?= htmlspecialchars($user['email']) ?></p>
<p>Rôle: <?= htmlspecialchars($user['role']) ?></p>

<p><a href="/lol-portal/index.php">Accueil</a></p>
</body>
</html>

