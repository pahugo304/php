<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

$user = current_user();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'LoL Portal') ?></title>
  <link rel="stylesheet" href="/lol-portal/assets/css/style.css">
</head>
<body>
<header class="topbar">
  <div class="container topbar__inner">
    <a class="brand" href="/lol-portal/index.php">LoL Portal</a>

    <nav class="nav">
      <a href="/lol-portal/index.php">Accueil</a>

      <?php if (!$user): ?>
        <a href="/lol-portal/register.php">Inscription</a>
        <a class="btn btn--ghost" href="/lol-portal/login.php">Connexion</a>
      <?php else: ?>
        <a href="/lol-portal/profile.php">Mon profil</a>
        <?php if (is_admin()): ?>
          <a href="/lol-portal/admin/dashboard.php">Admin</a>
        <?php endif; ?>
        <a class="btn btn--ghost" href="/lol-portal/logout.php">Logout</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="container">
