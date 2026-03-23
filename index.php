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
    <p>Connecte-toi ou crée un compte pour avoir accès aux fonctionnalités du site.</p>
  <?php endif; ?>

  <p class="muted">Bienvenue sur LoL Portal !</p>
<p class="muted" style="max-width:700px; line-height:1.7;">
  LoL Portal est une plateforme de découverte et de gestion de jeux vidéo.
  Les utilisateurs peuvent consulter les jeux disponibles, découvrir leurs succès
  et accéder à leur profil personnel.
</p>
  <div class="actions">
    <a class="btn" href="/lol-portal/games.php">Voir les jeux</a>
    <?php if (!$user): ?>
      <a class="btn btn--ghost" href="/lol-portal/login.php">Connexion</a>
    <?php endif; ?>
  </div>
</section>

<?php site_footer(); ?>