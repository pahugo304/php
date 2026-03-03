<?php
require_once __DIR__ . '/includes/layout.php';

$user = current_user();
site_header('Accueil');
?>

<section class="card">
  <h1>LoL Portal</h1>

  <?php if ($user): ?>
    <div class="alert">
      Connecté en tant que <strong><?= htmlspecialchars($user['username']) ?></strong>
      (<?= htmlspecialchars($user['role']) ?>)
    </div>
  <?php else: ?>
    <p>Bienvenue ! Connecte-toi ou crée un compte.</p>
  <?php endif; ?>

  <p class="muted">Objectif : auth + rôles + CRUD jeux + succès.</p>

  <div class="actions">
    <a class="btn" href="/lol-portal/games.php">Voir les jeux</a>
    <?php if (!$user): ?>
      <a class="btn btn--ghost" href="/lol-portal/login.php">Connexion</a>
    <?php endif; ?>
  </div>
</section>

<?php site_footer(); ?>