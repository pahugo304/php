<?php
$title = "Admin Dashboard";
require_once __DIR__ . '/../includes/header.php';

require_admin();
?>

<div class="card">
  <h1>Admin Dashboard</h1>
  <p class="muted">
    Connecté : <strong><?= htmlspecialchars($user['username']) ?></strong>
    (<?= htmlspecialchars($user['role']) ?>)
  </p>

  <hr>

  <div style="display:flex; gap:12px; flex-wrap:wrap;">
    <a class="btn" href="/lol-portal/admin/users.php">Gérer les utilisateurs</a>
    <a class="btn" href="/lol-portal/admin/games.php">Gérer les jeux</a>
    <a class="btn" href="/lol-portal/admin/achievements.php">Gérer les succès</a>
  </div>

  <hr>
  <a class="btn btn--ghost" href="/lol-portal/index.php">Retour site</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>