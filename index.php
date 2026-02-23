<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$user = current_user();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>LoL Portal</title>
</head>
<body>
  <h1>LoL Portal</h1>

  <?php if ($user): ?>
    <p>Connecté en tant que <strong><?= htmlspecialchars($user['username']) ?></strong> (<?= htmlspecialchars($user['role']) ?>)</p>
    <p>
      <a href="/lol-portal/profile.php">Mon profil</a> |
      <?php if (is_admin()): ?>
        <a href="/lol-portal/admin/dashboard.php">Admin</a> |
      <?php endif; ?>
      <a href="/lol-portal/logout.php">Logout</a>
    </p>
  <?php else: ?>
    <p><a href="/lol-portal/register.php">Inscription</a> | <a href="/lol-portal/login.php">Connexion</a></p>
  <?php endif; ?>

  <hr>
  <p>Objectif : auth + rôles + CRUD jeux + succès.</p>
</body>
</html>
