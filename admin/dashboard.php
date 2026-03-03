<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();
$user = current_user();
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Admin - Dashboard</title></head>
<body>
<h1>Admin Dashboard</h1>
<p>Connecté : <strong><?= htmlspecialchars($user['username']) ?></strong> (admin)</p>

<ul>
  <li><a href="/lol-portal/admin/users.php">Gérer les utilisateurs</a></li>
  <li><a href="/lol-portal/admin/games.php">Gérer les jeux</a></li>
  <li><a href="/lol-portal/admin/achievements.php">Gérer les succès</a></li>
</ul>

<p><a href="/lol-portal/index.php">Retour site</a></p>
</body>
</html>
