<?php
$title = "Accueil";
require_once __DIR__ . '/includes/header.php';
?>

<div class="card">
  <h1>LoL Portal</h1>

  <?php if ($user): ?>
    <div class="flash">
      Connecté en tant que <strong><?= htmlspecialchars($user['username']) ?></strong>
      (<?= htmlspecialchars($user['role']) ?>)
    </div>
  <?php else: ?>
    <div class="flash">
      Bienvenue ! Connecte-toi pour accéder aux fonctionnalités.
    </div>
  <?php endif; ?>

  <hr>
  <p class="muted">Objectif : auth + rôles + CRUD jeux + succès.</p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>