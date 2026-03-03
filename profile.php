<?php
$title = "Profil";
require_once __DIR__ . '/includes/header.php';

require_login();
$user = current_user();
?>

<div class="card">
  <h1>Profil</h1>

  <p>Username : <strong><?= htmlspecialchars($user['username']) ?></strong></p>
  <p>Email : <?= htmlspecialchars($user['email']) ?></p>
  <p>Rôle : <?= htmlspecialchars($user['role']) ?></p>

  <hr>
  <p><a class="btn btn--ghost" href="/lol-portal/index.php">Retour accueil</a></p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>