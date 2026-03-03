<?php
require_once __DIR__ . '/../includes/layout.php';

require_admin();
$user = current_user();

site_header('Admin - Dashboard');
?>

<section class="card">
  <h1>Admin Dashboard</h1>
  <div class="alert">Connecté : <strong><?= htmlspecialchars($user['username']) ?></strong> (<?= htmlspecialchars($user['role']) ?>)</div>

  <div class="grid">
    <a class="tile tile--link" href="/lol-portal/admin/users.php"><strong>Gérer les utilisateurs</strong><p class="muted">Promote/Demote/Delete</p></a>
    <a class="tile tile--link" href="/lol-portal/admin/games.php"><strong>Gérer les jeux</strong><p class="muted">CRUD Games</p></a>
    <a class="tile tile--link" href="/lol-portal/admin/achievements.php"><strong>Gérer les succès</strong><p class="muted">CRUD Achievements</p></a>
  </div>

  <p class="muted"><a href="/lol-portal/index.php">Retour site</a></p>
</section>

<?php site_footer(); ?>